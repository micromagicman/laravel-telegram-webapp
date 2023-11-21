<?php

use Micromagicman\TelegramWebApp\Service\TelegramWebAppService;
use Micromagicman\TelegramWebApp\Dto\TelegramUser;

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