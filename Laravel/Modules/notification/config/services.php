<?php

return [
    'sms_ru' => [
        'endpoint' => env('SMSRU_ENDPOINT', 'https://sms.ru/sms/send'),
        'api_id' => env('SMSRU_API_ID'),
    ],
    'firebase' => [
        "credentials" => env('FIREBASE_CREDENTIALS')
    ]
];
