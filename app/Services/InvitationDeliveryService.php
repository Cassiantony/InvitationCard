<?php

namespace App\Services;

use App\Mail\InvitationCardMail;
use App\Models\CardDesign;
use App\Models\Event;
use App\Models\InvitationDelivery;
use App\Models\Invitee;
use App\Models\User;
use App\Services\Messaging\SmsGateway;
use App\Services\Messaging\WhatsAppGateway;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvitationDeliveryService
{
    public function __construct(
        private InvitationCardPdfComposer $composer,
        private WhatsAppGateway $whatsApp,
        private SmsGateway $sms,
    ) {}

    /**
     * @return array{success: bool, method: string, cost_tsh: int, recipient: string, api_response: ?string, fallback_method: ?string, error: ?string}
     */
    public function deliver(
        Event $event,
        Invitee $invitee,
        CardDesign $design,
        User $user,
        string $mode = 'auto',
        bool $isResend = false,
        ?string $forcedMethod = null,
    ): array {
        $method = $forcedMethod ?? $this->resolveMethod($mode);

        if ($method === 'email') {
            return $this->deliverEmail($event, $invitee, $design, $isResend);
        }

        if ($method === 'sms') {
            return $this->deliverSms($event, $invitee);
        }

        if ($method === 'whatsapp') {
            return $this->deliverWhatsApp($event, $invitee, $design, allowSmsFallback: ! $isResend && $mode === 'auto');
        }

        return $this->deliverWhatsApp($event, $invitee, $design, allowSmsFallback: true);
    }

    public function calculateResendCost(Invitee $invitee, string $method): int
    {
        return match ($method) {
            'email' => 0,
            'sms' => (int) config('invitation.cost_sms_tsh', 500),
            'whatsapp' => $this->whatsappResendCost($invitee),
            default => (int) config('invitation.cost_per_card_tsh', 500),
        };
    }

    public function whatsappResendCost(Invitee $invitee): int
    {
        $lastWhatsApp = InvitationDelivery::query()
            ->where('invitee_id', $invitee->id)
            ->where('delivery_method', 'whatsapp')
            ->where('status', 'sent')
            ->orderByDesc('sent_at')
            ->first();

        if (! $lastWhatsApp?->sent_at) {
            return (int) config('invitation.cost_whatsapp_resend_tsh', 500);
        }

        $freeHours = (int) config('invitation.whatsapp_free_resend_hours', 20);

        if ($lastWhatsApp->sent_at->diffInHours(now()) < $freeHours) {
            return 0;
        }

        return (int) config('invitation.cost_whatsapp_resend_tsh', 500);
    }

    /**
     * @return array{success: bool, charged_tsh: int, delivery_method: string, recipient: string, api_response: ?string, fallback_method: ?string, error_message: ?string}
     */
    public function sendToInvitee(
        Event $event,
        Invitee $invitee,
        CardDesign $design,
        User $user,
        string $mode,
        bool $isResend,
        ?string $forcedMethod = null,
    ): array {
        $method = $forcedMethod ?? $this->resolveMethod($mode);
        $cost = $isResend
            ? $this->calculateResendCost($invitee, $method)
            : (int) config('invitation.cost_per_card_tsh', 500);

        if ((int) $user->wallet_balance < $cost) {
            return [
                'success' => false,
                'charged_tsh' => 0,
                'delivery_method' => $method,
                'recipient' => $this->primaryRecipient($invitee, $method),
                'api_response' => null,
                'fallback_method' => null,
                'error_message' => 'Insufficient wallet balance. Need Tsh '.number_format($cost).'.',
            ];
        }

        $result = $this->deliver($event, $invitee, $design, $user, $mode, $isResend, $forcedMethod);

        if (! $result['success']) {
            return [
                'success' => false,
                'charged_tsh' => 0,
                'delivery_method' => $result['method'],
                'recipient' => $result['recipient'],
                'api_response' => $result['api_response'],
                'fallback_method' => $result['fallback_method'],
                'error_message' => $result['error'],
            ];
        }

        if ($cost > 0) {
            $user->decrement('wallet_balance', $cost);
            $user->refresh();
        }

        $invitee->update([
            'status' => 'sent',
            'invited_at' => now(),
        ]);

        return [
            'success' => true,
            'charged_tsh' => $cost,
            'delivery_method' => $result['method'],
            'recipient' => $result['recipient'],
            'api_response' => $result['api_response'],
            'fallback_method' => $result['fallback_method'],
            'error_message' => null,
        ];
    }

    private function resolveMethod(string $mode): string
    {
        return match ($mode) {
            'whatsapp' => 'whatsapp',
            'sms' => 'sms',
            'email' => 'email',
            default => 'auto',
        };
    }

    private function deliverWhatsApp(Event $event, Invitee $invitee, CardDesign $design, bool $allowSmsFallback): array
    {
        if (empty($invitee->phone)) {
            return $this->fail('whatsapp', $invitee->phone ?: '—', 'Invitee has no phone number.');
        }

        $imagePath = null;

        try {
            $imagePath = $this->composer->composeImage($design, $invitee);
            $caption = "You are invited to {$event->title}. Please open your invitation card.";
            $wa = $this->whatsApp->sendImage($invitee->phone, $imagePath, $caption, $event->title);

            if ($wa->success) {
                return [
                    'success' => true,
                    'method' => 'whatsapp',
                    'cost_tsh' => 0,
                    'recipient' => $invitee->phone,
                    'api_response' => $wa->message,
                    'fallback_method' => null,
                    'error' => null,
                ];
            }

            if (! $wa->onWhatsApp && $allowSmsFallback) {
                $sms = $this->deliverSms($event, $invitee);

                return [
                    'success' => $sms['success'],
                    'method' => $sms['success'] ? 'sms' : 'whatsapp',
                    'cost_tsh' => 0,
                    'recipient' => $invitee->phone,
                    'api_response' => $sms['success']
                        ? $wa->message.' → SMS fallback: '.$sms['api_response']
                        : $wa->message,
                    'fallback_method' => $sms['success'] ? 'sms' : null,
                    'error' => $sms['success'] ? null : ($sms['error'] ?? $wa->message),
                ];
            }

            return $this->fail('whatsapp', $invitee->phone, $wa->message, $wa->message);
        } catch (\Throwable $e) {
            Log::error('WhatsApp delivery failed', ['invitee_id' => $invitee->id, 'error' => $e->getMessage()]);

            return $this->fail('whatsapp', $invitee->phone, $e->getMessage());
        } finally {
            if ($imagePath && is_file($imagePath)) {
                @unlink($imagePath);
            }
        }
    }

    private function deliverSms(Event $event, Invitee $invitee): array
    {
        if (empty($invitee->phone)) {
            return $this->fail('sms', $invitee->phone ?: '—', 'Invitee has no phone number.');
        }

        $sms = $this->sms->sendInvitationCode($invitee->phone, $event, $invitee);

        if ($sms->success) {
            return [
                'success' => true,
                'method' => 'sms',
                'cost_tsh' => 0,
                'recipient' => $invitee->phone,
                'api_response' => $sms->message,
                'fallback_method' => null,
                'error' => null,
            ];
        }

        return $this->fail('sms', $invitee->phone, $sms->message, $sms->message);
    }

    private function deliverEmail(Event $event, Invitee $invitee, CardDesign $design, bool $isResend): array
    {
        if (empty($invitee->email)) {
            return $this->fail('email', $invitee->email ?: '—', 'Invitee has no email address.');
        }

        $imagePath = null;

        try {
            $imagePath = $this->composer->composeImage($design, $invitee);
            $safe = preg_replace('/[^a-zA-Z0-9_-]+/', '-', (string) $invitee->name) ?: 'invitee';
            $filename = "invitation-{$safe}-{$invitee->invitation_code}.png";

            Mail::to($invitee->email)->send(
                new InvitationCardMail($event, $invitee, $imagePath, $filename, 'image/png')
            );

            return [
                'success' => true,
                'method' => 'email',
                'cost_tsh' => 0,
                'recipient' => $invitee->email,
                'api_response' => $isResend ? 'Resent via email (free).' : 'Sent via email.',
                'fallback_method' => null,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('Email delivery failed', ['invitee_id' => $invitee->id, 'error' => $e->getMessage()]);

            return $this->fail('email', $invitee->email, $e->getMessage());
        } finally {
            if ($imagePath && is_file($imagePath)) {
                @unlink($imagePath);
            }
        }
    }

    private function primaryRecipient(Invitee $invitee, string $method): string
    {
        return match ($method) {
            'email' => $invitee->email ?: '—',
            default => $invitee->phone ?: '—',
        };
    }

    /**
     * @return array{success: false, method: string, cost_tsh: int, recipient: string, api_response: ?string, fallback_method: null, error: string}
     */
    private function fail(string $method, string $recipient, string $error, ?string $apiResponse = null): array
    {
        return [
            'success' => false,
            'method' => $method,
            'cost_tsh' => 0,
            'recipient' => $recipient ?: '—',
            'api_response' => $apiResponse,
            'fallback_method' => null,
            'error' => $error,
        ];
    }
}
