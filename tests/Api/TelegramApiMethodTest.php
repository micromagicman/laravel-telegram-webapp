<?php

namespace Micromagicman\TelegramWebApp\Tests\Api;

use Micromagicman\TelegramWebApp\Api\TelegramApiMethod;
use Micromagicman\TelegramWebApp\Api\WebApp\AnswerWebAppQuery;
use PHPUnit\Framework\TestCase;

class TelegramApiMethodTest extends TestCase
{
    public function testMethodName()
    {
        $method = new AnswerWebAppQuery( [] );
        $this->assertEquals( "answerWebAppQuery", $method->methodName() );
        $method = new SomeAnotherMethod();
        $this->assertEquals( "someAnotherMethod", $method->methodName() );
    }
}

class SomeAnotherMethod extends TelegramApiMethod
{
    public function body(): array
    {
        return [];
    }
}
