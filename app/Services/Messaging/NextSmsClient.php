<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NextSmsClient
{
    public function isConfigured(): bool
    {
        return ! empty(config('services.nextsms.username'))
            && ! empty(config('services.nextsms.password'));
    }

    /**
     * @return array{success: bool, message: string, reference: ?string}
     */
    public function sendSingle(string $phone, string $text): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'NextSMS API is not configured.',
                'reference' => null,
            ];
        }

        $url = rtrim(config('services.nextsms.base_url'), '/')
            .'/api/sms/v1/text/single';

        $payload = [
            'from' => config('services.nextsms.sender_id', 'NEXTSMS'),
            'to' => $phone,
            'text' => $text,
        ];

        $request = Http::acceptJson()->asJson();

        if ($apiKey = config('services.nextsms.api_key')) {
            $request = $request->withHeaders(['Authorization' => 'Basic '.$apiKey]);
        } else {
            $request = $request->withBasicAuth(
                config('services.nextsms.username'),
                config('services.nextsms.password')
            );
        }

        try {
            $response = $request->post($url, $payload);
            $body = $response->json() ?? [];

            if ($response->successful() && $this->responseIndicatesSuccess($body)) {
                return [
                    'success' => true,
                    'message' => $this->extractSuccessMessage($body),
                    'reference' => $this->extractReference($body),
                ];
            }

            $error = $this->extractErrorMessage($body, $response->body());

            Log::warning('NextSMS send failed', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $body,
            ]);

            return [
                'success' => false,
                'message' => $error,
                'reference' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('NextSMS request error', ['phone' => $phone, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'reference' => null,
            ];
        }
    }

    private function responseIndicatesSuccess(array $body): bool
    {
        if (isset($body['successful']) && $body['successful'] === true) {
            return true;
        }

        if (isset($body['status']) && in_array(strtolower((string) $body['status']), ['success', 'sent', 'ok'], true)) {
            return true;
        }

        if (isset($body['code']) && (int) $body['code'] === 200) {
            return true;
        }

        // Some responses return messages array on success.
        return isset($body['messages']) && is_array($body['messages']) && count($body['messages']) > 0;
    }

    private function extractSuccessMessage(array $body): string
    {
        if (! empty($body['message']) && is_string($body['message'])) {
            return $body['message'];
        }

        $reference = $this->extractReference($body);

        return $reference
            ? "SMS sent via NextSMS (ref: {$reference})."
            : 'SMS sent via NextSMS.';
    }

    private function extractReference(array $body): ?string
    {
        foreach (['messageId', 'message_id', 'requestId', 'request_id', 'id'] as $key) {
            if (! empty($body[$key])) {
                return (string) $body[$key];
            }
        }

        if (! empty($body['messages'][0]['messageId'])) {
            return (string) $body['messages'][0]['messageId'];
        }

        return null;
    }

    private function extractErrorMessage(array $body, string $raw): string
    {
        if (! empty($body['message']) && is_string($body['message'])) {
            return $body['message'];
        }

        if (! empty($body['error']) && is_string($body['error'])) {
            return $body['error'];
        }

        if (! empty($body['errors']) && is_array($body['errors'])) {
            return implode('; ', array_map('strval', $body['errors']));
        }

        return $raw !== '' ? $raw : 'NextSMS rejected the message.';
    }
}
