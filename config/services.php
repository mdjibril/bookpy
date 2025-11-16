<?php
// This file contains service configurations, including the email service setup.

return [
    'email' => [
        'driver' => 'smtp',
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'your_email@example.com',
        'password' => 'your_password',
        'encryption' => 'tls',
        'from' => [
            'address' => 'no-reply@example.com',
            'name' => 'Bookpy Notifications',
        ],
    ],
    'notification' => [
        'enabled' => true,
        'channels' => ['email', 'sms'],
    ],
];