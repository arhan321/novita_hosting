<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VAPID Keys for Web Push
    |--------------------------------------------------------------------------
    | Generate with: php -r "use Minishlink\WebPush\VAPID; require 'vendor/autoload.php'; print_r(VAPID::createVapidKeys());"
    */
    'vapid' => [
        'subject'     => env('VAPID_SUBJECT', 'mailto:admin@example.com'),
        'public_key'  => env('VAPID_PUBLIC_KEY', ''),
        'private_key' => env('VAPID_PRIVATE_KEY', ''),
    ],
];
