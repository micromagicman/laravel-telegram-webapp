<?php

namespace Micromagicman\TelegramWebApp\Api;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Micromagicman\TelegramWebApp\Api\WebApp\AnswerWebAppQuery;
use Micromagicman\TelegramWebApp\Service\TelegramWebAppService;

/**
 * @deprecated Use {@link TelegramWebAppService}.
 */
class TelegramBotApi implements TelegramApi
{

    /**
     * Root endpoint for Telegram Bot API
     */
    private const TELEGRAM_API_ROOT = 'https://api.telegram.org';

    /**
     * Root endpoint with current bot token
     */
    private string $apiBotRoot;

    public function __construct()
    {
        $this->apiBotRoot = self::TELEGRAM_API_ROOT . '/bot' . telegramToken();
    }

    /**
     * Set the result of an interaction with a Web App and send a corresponding message on behalf of the user to the chat
     * from which the query originated. On success, an Illuminate\Http\Client\Response object is returned.
     */
    public function answerWebAppQuery( mixed $result ): Response
    {
        return $this->execute( new AnswerWebAppQuery( $result ) );
    }

    /**
     * Telegram Bot API method execution
     */
    public function execute( TelegramApiMethod $apiMethod ): Response
    {
        return Http::post( $this->prepareUrl( $apiMethod ), $apiMethod->body() );
    }

    /**
     * Create endpoint for Telegram Bot API
     */
    private function prepareUrl( TelegramApiMethod $apiMethod ): string
    {
        return "$this->apiBotRoot/{$apiMethod->methodName()}";
    }
}