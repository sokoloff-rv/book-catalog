<?php

namespace app\services;

final class SmsResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $status,
        public readonly ?string $raw = null
    ) {
    }
}
