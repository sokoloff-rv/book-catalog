<?php

namespace app\services;

interface SmsServiceInterface
{
    /**
     * @return array{success: bool, status: string, raw: string|null}
     */
    public function send(string $phone, string $text): array;
}
