<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Log;

class WhatsAppGateway
{
    public function __construct(
        private MetaWhatsAppClient $meta,
    ) {}

    public function isConfigured(): bool
    {
        return $this->meta->isConfigured();
    }

    public function sendImage(string $phone, string $imagePath, string $caption, ?string $eventTitle = null): WhatsAppSendResult
    {
        $phone = $this->normalizePhone($phone);

        if ($phone === '') {
            return new WhatsAppSendResult(false, false, 'Invalid phone number.');
        }

        if (! is_readable($imagePath)) {
            return new WhatsAppSendResult(false, false, 'Invitation image is missing.');
        }

        if ($this->useDemoMode()) {
            return $this->demoSend($phone, $caption);
        }

        if (! $this->isConfigured()) {
            return new WhatsAppSendResult(false, false, 'Meta WhatsApp API is not configured. Set WHATSAPP_ACCESS_TOKEN and WHATSAPP_PHONE_NUMBER_ID.');
        }

        $result = $this->meta->sendInvitationImage($phone, $imagePath, $caption, $eventTitle);

        return new WhatsAppSendResult(
            $result['success'],
            $result['on_whatsapp'],
            $result['message'],
        );
    }

    public function checkOnWhatsApp(string $phone): bool
    {
        $phone = $this->normalizePhone($phone);

        if ($phone === '') {
            return false;
        }

        if ($this->useDemoMode()) {
            return $this->demoOnWhatsApp($phone);
        }

        // Meta Cloud API has no official pre-check; delivery attempt determines availability.
        return true;
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

    private function demoOnWhatsApp(string $phone): bool
    {
        $digits = preg_replace('/\D/', '', $phone) ?: '0';

        return ((int) substr($digits, -1)) % 2 === 0;
    }

    private function demoSend(string $phone, string $caption): WhatsAppSendResult
    {
        if (! $this->demoOnWhatsApp($phone)) {
            return new WhatsAppSendResult(
                false,
                false,
                'Number is not registered on WhatsApp (demo: phone ending in odd digit).'
            );
        }

        Log::info('WhatsApp demo send', ['phone' => $phone, 'caption' => $caption]);

        return new WhatsAppSendResult(true, true, 'Delivered via WhatsApp (demo mode).');
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
