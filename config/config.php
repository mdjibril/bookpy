<?php
// Configuration settings for the application

return [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'bookpy',
        'user' => 'root',
        'password' => '',
    ],
    'email' => [
        'api_key' => 'your_api_key_here',
        'from_address' => 'no-reply@bookpy.com',
    ],
    'app' => [
        'debug' => true,
        'base_url' => 'http://localhost/bookpy',
    ],
];
