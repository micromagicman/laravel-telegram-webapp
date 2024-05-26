<?php

namespace Micromagicman\TelegramWebApp\Api;

/**
 * Abstract Telegram API
 * Possible implementations:
 * <ul>
 *  <li>{@link https://core.telegram.org/#getting-started Telegram API}</li>
 *  <li>{@link https://core.telegram.org/bots/api Telegram Telegram Bot API}</li>
 * </ul>
 */
interface TelegramApi
{

    /**
     * Execution of API method
     */
    public function execute( TelegramApiMethod $apiMethod ): mixed;
}