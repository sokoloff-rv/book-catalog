<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'smsPilotApiKey' => $_ENV['SMSPILOT_API_KEY'] ?? null,
];
