<?php

use Micromagicman\TelegramWebApp\Dto\TelegramUser;
use Micromagicman\TelegramWebApp\Service\TelegramWebAppService;

if ( !function_exists( 'telegramWebApp' ) ) {

    /**
     * Get Telegram MiniApp Service
     */
    function telegramWebApp(): TelegramWebAppService
    {
        return app( TelegramWebAppService::class );
    }
}

if ( !function_exists( 'telegramUser' ) ) {

    /**
     * Get Telegram MiniApp User
     */
    function telegramUser(): TelegramUser
    {
        return telegramWebApp()->getWebAppUser();
    }
}

if ( !function_exists( 'webAppConfig' ) ) {

    /**
     * Receive configuration value by the key with plugin prefix
     */
    function webAppConfig( string $key, mixed $defaultValue = null ): mixed
    {
        return config( "telegram-webapp.$key", $defaultValue );
    }
}

if ( !function_exists( 'telegramToken' ) ) {

    /**
     * Get Telegram bot token or fail if token not provided
     */
    function telegramToken(): string
    {
        $token = webAppConfig( 'botToken' );
        if ( !is_string( $token ) || empty( $token ) ) {
            throw new UnexpectedValueException( 'Telegram bot token is not valid' );
        }
        return $token;
    }
}

if ( !function_exists( 'webAppQueryId' ) ) {

    /**
     * Get Web App query_id from request's query parameters
     */
    function webAppQueryId(): ?string
    {
        return request()->query( 'query_id' );
    }
}