<?php

namespace App\Services\Messaging;

class SmsSendResult
{
    public function __construct(
        public bool $success,
        public string $message,
    ) {}
}
