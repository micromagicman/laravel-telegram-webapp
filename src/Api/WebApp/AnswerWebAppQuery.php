<?php

namespace Micromagicman\TelegramWebApp\Api\WebApp;

use Micromagicman\TelegramWebApp\Api\TelegramApiMethod;

/**
 * Set the result of an interaction with a Web App and send a corresponding message on behalf of the user
 * to the chat from which the query originated.
 *
 * @see {@link https://core.telegram.org/bots/api#answerwebappquery}
 * @deprecated Use {@link TelegramWebAppService}.
*/
class AnswerWebAppQuery extends TelegramApiMethod
{

    /**
     * A JSON-serialized object describing the message to be sent
     */
    private mixed $result;

    public function __construct( mixed $result )
    {
        $this->result = $result;
    }

    public function body(): array
    {
        return [
            'web_app_query_id' => webAppQueryId(),
            'result' => json_encode( $this->result )
        ];
    }
}