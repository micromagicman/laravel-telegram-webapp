<?php

namespace Micromagicman\TelegramWebApp\Api;

/**
 * Abstract Telegram API
 * Possible implementations:
 * <ul>
 *  <li>{@link https://core.telegram.org/#getting-started Telegram API}</li>
 *  <li>{@link https://core.telegram.org/bots/api Telegram Telegram Bot API}</li>
 * </ul>
 * @deprecated Use {@link TelegramWebAppService}.
 */
interface TelegramApi
{

    /**
     * Execution of API method
     */
    public function execute( TelegramApiMethod $apiMethod ): mixed;
}