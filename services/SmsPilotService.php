<?php

namespace app\services;

use yii\base\Component;

class SmsPilotService extends Component implements SmsServiceInterface
{
    public string $apiKey = '';
    public string $endpoint = 'https://smspilot.ru/api2.php';

    public function init(): void
    {
        parent::init();

        if (!$this->apiKey) {
            $this->apiKey = $_ENV['SMS_API_KEY']
                ?? $_ENV['SMS_EMULATOR_API_KEY']
                ?? '';
        }
    }

    public function send(string $phone, string $text): array
    {
        $payload = [
            'apikey' => $this->apiKey,
            'send' => [
                [
                    'to' => $phone,
                    'text' => $text,
                ],
            ],
            'format' => 'json',
        ];

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'timeout' => 5,
            ],
        ]);

        try {
            $result = file_get_contents($this->endpoint, false, $context);
        } catch (\Throwable $exception) {
            return [
                'success' => false,
                'status' => 'exception',
                'raw' => $exception->getMessage(),
            ];
        }

        if ($result === false) {
            return [
                'success' => false,
                'status' => 'network_error',
                'raw' => null,
            ];
        }

        return [
            'success' => true,
            'status' => 'sent',
            'raw' => $result,
        ];
    }
}
