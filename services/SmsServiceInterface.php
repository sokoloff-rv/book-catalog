<?php

namespace app\services;

interface SmsServiceInterface
{
    public function send(string $phone, string $text): SmsResult;
}
