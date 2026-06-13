<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MetaWhatsAppClient
{
    public function isConfigured(): bool
    {
        return ! empty(config('services.whatsapp.access_token'))
            && ! empty(config('services.whatsapp.phone_number_id'));
    }

    /**
     * @return array{success: bool, on_whatsapp: bool, message: string, message_id: ?string}
     */
    public function sendInvitationImage(string $phone, string $imagePath, string $caption, ?string $eventTitle = null): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'on_whatsapp' => false,
                'message' => 'Meta WhatsApp API is not configured.',
                'message_id' => null,
            ];
        }

        try {
            $mediaId = $this->uploadMedia($imagePath);

            if (config('services.whatsapp.template_name')) {
                return $this->sendTemplateWithImage($phone, $mediaId, $eventTitle ?? $caption);
            }

            return $this->sendImageMessage($phone, $mediaId, $caption);
        } catch (MetaWhatsAppException $e) {
            return [
                'success' => false,
                'on_whatsapp' => ! $e->notOnWhatsApp,
                'message' => $e->getMessage(),
                'message_id' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('Meta WhatsApp send failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'on_whatsapp' => false,
                'message' => $e->getMessage(),
                'message_id' => null,
            ];
        }
    }

    public function uploadMedia(string $imagePath): string
    {
        $mime = mime_content_type($imagePath) ?: 'image/png';
        $url = $this->graphUrl(config('services.whatsapp.phone_number_id').'/media');

        $response = Http::withToken(config('services.whatsapp.access_token'))
            ->attach('file', file_get_contents($imagePath), basename($imagePath), ['Content-Type' => $mime])
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'type' => $mime,
            ]);

        if (! $response->successful()) {
            $this->throwFromResponse($response->json(), 'Failed to upload WhatsApp media.');
        }

        $mediaId = $response->json('id');
        if (! $mediaId) {
            throw new RuntimeException('WhatsApp media upload did not return a media ID.');
        }

        return $mediaId;
    }

    /**
     * @return array{success: bool, on_whatsapp: bool, message: string, message_id: ?string}
     */
    private function sendImageMessage(string $phone, string $mediaId, string $caption): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatTo($phone),
            'type' => 'image',
            'image' => array_filter([
                'id' => $mediaId,
                'caption' => $caption !== '' ? $caption : null,
            ]),
        ];

        $response = $this->postMessage($payload);

        if (! $response->successful()) {
            $this->throwFromResponse($response->json(), 'Failed to send WhatsApp image.');
        }

        return [
            'success' => true,
            'on_whatsapp' => true,
            'message' => 'Delivered via Meta WhatsApp.',
            'message_id' => $response->json('messages.0.id'),
        ];
    }

    /**
     * @return array{success: bool, on_whatsapp: bool, message: string, message_id: ?string}
     */
    private function sendTemplateWithImage(string $phone, string $mediaId, string $eventTitle): array
    {
        $language = config('services.whatsapp.template_language', 'en');
        $templateName = config('services.whatsapp.template_name');

        $components = [
            [
                'type' => 'header',
                'parameters' => [
                    [
                        'type' => 'image',
                        'image' => ['id' => $mediaId],
                    ],
                ],
            ],
        ];

        if (config('services.whatsapp.template_body_param', true)) {
            $components[] = [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => mb_substr($eventTitle, 0, 1024)],
                ],
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatTo($phone),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $language],
                'components' => $components,
            ],
        ];

        $response = $this->postMessage($payload);

        if (! $response->successful()) {
            $this->throwFromResponse($response->json(), 'Failed to send WhatsApp template.');
        }

        return [
            'success' => true,
            'on_whatsapp' => true,
            'message' => 'Delivered via Meta WhatsApp template.',
            'message_id' => $response->json('messages.0.id'),
        ];
    }

    private function postMessage(array $payload): \Illuminate\Http\Client\Response
    {
        $url = $this->graphUrl(config('services.whatsapp.phone_number_id').'/messages');

        return Http::withToken(config('services.whatsapp.access_token'))
            ->acceptJson()
            ->post($url, $payload);
    }

    private function graphUrl(string $path): string
    {
        $version = config('services.whatsapp.graph_version', 'v21.0');
        $path = ltrim($path, '/');

        return "https://graph.facebook.com/{$version}/{$path}";
    }

    private function formatTo(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?: '';

        return '+'.$digits;
    }

    private function throwFromResponse(?array $body, string $fallback): void
    {
        $error = $body['error'] ?? [];
        $message = $error['message'] ?? $fallback;
        $code = $error['code'] ?? null;
        $notOnWhatsApp = $this->isNotOnWhatsAppError($error, $message);

        throw new MetaWhatsAppException($message, (int) ($code ?? 0), $notOnWhatsApp);
    }

    private function isNotOnWhatsAppError(array $error, string $message): bool
    {
        $code = (int) ($error['code'] ?? 0);
        $lower = strtolower($message);

        if (in_array($code, [131026, 63003, 63024, 131051], true)) {
            return true;
        }

        return str_contains($lower, 'not a whatsapp')
            || str_contains($lower, 'not on whatsapp')
            || str_contains($lower, 'undeliverable')
            || str_contains($lower, 'invalid user')
            || str_contains($lower, 'recipient phone number not valid');
    }
}

class MetaWhatsAppException extends RuntimeException
{
    public function __construct(
        string $message,
        int $code = 0,
        public bool $notOnWhatsApp = false,
    ) {
        parent::__construct($message, $code);
    }
}
