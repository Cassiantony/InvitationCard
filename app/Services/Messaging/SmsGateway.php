<?php

namespace App\Services\Messaging;

use App\Models\Event;
use App\Models\Invitee;
use Illuminate\Support\Facades\Log;

class SmsGateway
{
    public function __construct(
        private NextSmsClient $nextSms,
    ) {}

    public function isConfigured(): bool
    {
        return $this->nextSms->isConfigured();
    }

    public function sendInvitationCode(string $phone, Event $event, Invitee $invitee): SmsSendResult
    {
        $phone = $this->normalizePhone($phone);

        if ($phone === '') {
            return new SmsSendResult(false, 'Invalid phone number.');
        }

        $message = $this->buildMessage($event, $invitee);

        if ($this->useDemoMode()) {
            Log::info('SMS demo send', ['phone' => $phone, 'message' => $message]);

            return new SmsSendResult(true, 'SMS sent with invitation code (demo mode).');
        }

        if (! $this->isConfigured()) {
            return new SmsSendResult(false, 'NextSMS API is not configured. Set NEXTSMS_USERNAME and NEXTSMS_PASSWORD.');
        }

        $result = $this->nextSms->sendSingle($phone, $message);

        return new SmsSendResult($result['success'], $result['message']);
    }

    public function buildMessage(Event $event, Invitee $invitee): string
    {
        $link = route('invitee.show', ['code' => $invitee->invitation_code], true);

        return "You are invited to {$event->title}. Your invitation code: {$invitee->invitation_code}. RSVP: {$link}";
    }

    private function useDemoMode(): bool
    {
        if (! config('invitation.demo_messaging')) {
            return false;
        }

        if ($this->isConfigured()) {
            return false;
        }

        return app()->environment('local');
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) < 9) {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            $digits = '255'.substr($digits, 1);
        }

        if (! str_starts_with($digits, '255') && strlen($digits) === 9) {
            $digits = '255'.$digits;
        }

        return $digits;
    }
}
