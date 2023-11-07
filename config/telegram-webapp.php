<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram WebApp data validation switch
    |--------------------------------------------------------------------------
    | TODO
    */
    'enabled' => (bool)env( 'TELEGRAM_WEBAPP_DATA_VALIDATION_ENABLED', true ),

    'webAppScriptLocation' => 'https://telegram.org/js/telegram-web-app.js',

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    | TODO
    */
    'botToken' => env( 'TELEGRAM_BOT_TOKEN', '' ),

    /*
    |--------------------------------------------------------------------------
    | HTTP error response format options
    |--------------------------------------------------------------------------
    | TODO
    */
    'authDate' => [
        'validation' => false,
        'timeoutMillis' => 360000 // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP error response format options
    |--------------------------------------------------------------------------
    | TODO
    */
    'error' => [
        'status' => 403,
        'message' => 'Telegram WebApp data is not valid'
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram WebApp initData format options
    |--------------------------------------------------------------------------
    | TODO
    */
    'initData' => [
        'headers' => [
            'hash' => 'hash',
        ]
    ]
];