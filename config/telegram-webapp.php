<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram MiniApp data validation switch
    |--------------------------------------------------------------------------
    */
    'enabled' => (bool)env( 'TELEGRAM_WEBAPP_DATA_VALIDATION_ENABLED', true ),

    /*
    |--------------------------------------------------------------------------
    | Path to script (.js) which initializes Telegram MiniApp on your frontend app
    |--------------------------------------------------------------------------
    */
    'webAppScriptLocation' => 'https://telegram.org/js/telegram-web-app.js',

    /*
    |--------------------------------------------------------------------------
    | Your Telegram bot token
    |--------------------------------------------------------------------------
    */
    'botToken' => env( 'TELEGRAM_BOT_TOKEN', '' ),

    /*
    |--------------------------------------------------------------------------
    | HTTP error response format options
    |--------------------------------------------------------------------------
    */
    'error' => [
        // HTTP status code when Telegram MiniApp data validation fails
        'status' => 403,
        // Error message when Telegram MiniApp data validation fails
        // May contain placeholders for parameterizing the message
        'message' => 'Telegram WebApp data is not valid'
    ]
];