<?php

namespace Micromagicman\TelegramWebApp\Tests\Api;

use Illuminate\Config\Repository;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Micromagicman\TelegramWebApp\Api\TelegramBotApi;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TelegramBotApiTest extends TestCase
{
    use WithWorkbench;

    private TelegramBotApi $api;

    protected function defineEnvironment( $app )
    {
        tap( $app[ 'config' ], function ( Repository $config ) {
            $config->set( 'telegram-webapp.botToken', 'test-token' );
        } );
    }

    #[Test]
    public function testAnswerWebAppQuery()
    {
        Http::partialMock()
            ->shouldReceive( 'post' )
            ->once()
            ->andReturn( new Response( new \GuzzleHttp\Psr7\Response() ) );
        $api = new TelegramBotApi();
        $response = $api->answerWebAppQuery( [] );
        $this->assertEquals( 200, $response->status() );
        $this->assertEmpty( $response->body() );
    }
}