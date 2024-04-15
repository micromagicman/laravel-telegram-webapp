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
    | The lifetime of the {@link https://core.telegram.org/bots/webapps#webappinitdata Telegram initData} auth_date parameter in seconds.
    | The request to the server must be made within this interval, otherwise the data transmitted from Telegram
    | will be considered invalid. The values of the parameter <= 0 imply that there is no verification of the lifetime
    | of data from telegram and the auth_date parameter is not validated.
    |--------------------------------------------------------------------------
    */
    'authDateLifetimeSeconds' => 0,

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