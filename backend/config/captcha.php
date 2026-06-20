<?php

return [
    'yandex' => [
        'client_key' => env('YANDEX_SMARTCAPTCHA_CLIENT_KEY'),
        'server_key' => env('YANDEX_SMARTCAPTCHA_SERVER_KEY'),
        'validate_url' => 'https://smartcaptcha.cloud.yandex.ru/validate',
    ],
];
