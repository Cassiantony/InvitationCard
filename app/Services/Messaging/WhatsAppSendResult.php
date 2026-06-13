<?php

namespace App\Services\Messaging;

class WhatsAppSendResult
{
    public function __construct(
        public bool $success,
        public bool $onWhatsApp,
        public string $message,
    ) {}
}
