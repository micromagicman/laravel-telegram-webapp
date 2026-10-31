<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram MiniApp data validation switch
    |--------------------------------------------------------------------------
    */
    'enabled' => (bool) env('TELEGRAM_WEBAPP_DATA_VALIDATION_ENABLED', true),

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
    'botToken' => env('TELEGRAM_BOT_TOKEN', ''),

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
    | Third-party signature validation (Ed25519)
    |--------------------------------------------------------------------------
    | Telegram also signs initData with its own Ed25519 key, so the data can be
    | validated without the bot token — only the bot id is required. When
    | `enabled` is true, the `signature` field is verified against `publicKey`
    | instead of the bot-token `hash`. This is for Mini Apps you do not own the
    | bot token of.
    |--------------------------------------------------------------------------
    */
    'signature' => [
        // Verify the third-party `signature` field instead of the `hash` field
        'enabled' => (bool) env('TELEGRAM_WEBAPP_SIGNATURE_VALIDATION_ENABLED', false),
        // Ed25519 public key (hex) used to verify the signature. Defaults to
        // Telegram's production key.
        'publicKey' => env(
            'TELEGRAM_WEBAPP_SIGNATURE_PUBLIC_KEY',
            'e7bf03a2fa4602af4580703d88dda5bb59f32ed8b02a56c187fe7d34caed242d'
        ),
        // Numeric bot id (the part before ':' in the token) used to build the
        // data-check-string. Defaults to the id parsed from `botToken`.
        'botId' => env('TELEGRAM_WEBAPP_SIGNATURE_BOT_ID'),
    ],

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
        'message' => 'Telegram WebApp data is not valid',
    ],
];
