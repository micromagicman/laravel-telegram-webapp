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
     * Get Telegram MiniApp config value by key or default value
     */
    function webAppConfig( string $key, mixed $defaultValue = null ): TelegramUser
    {
        return telegramWebApp()->config( $key, $defaultValue );
    }
}