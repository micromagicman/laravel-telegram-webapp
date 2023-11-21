<?php

namespace Micromagicman\TelegramWebApp\Tests;

use DOMDocument;
use Illuminate\Foundation\Application;
use Micromagicman\TelegramWebApp\Dto\TelegramUser;
use Micromagicman\TelegramWebApp\Facade\TelegramFacade;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TelegramWebAppServiceProviderTest extends TestCase
{
    use WithWorkbench;

    protected function usesDisabledMode( Application $app ): void
    {
        $app[ 'config' ]->set( 'telegram-webapp.enabled', false );
    }

    protected function useTestTelegramBotToken( Application $app ): void
    {
        $app[ 'config' ]->set( 'telegram-webapp.botToken', 'test-token' );
    }

    #[Test]
    public function testWebAppPageLoadsCorrectly()
    {
        $webAppPageResponse = $this->get( '/' );
        $dom = $this->loadDOM( $webAppPageResponse->getContent() );
        $webAppScript = $dom->getElementById( "telegram-webapp-script" );
        $pageContentBlock = $dom->getElementById( "app-content" );

        $this->assertNotNull( $pageContentBlock );
        $this->assertNotNull( $webAppScript );
        $this->assertEquals( $webAppScript->getAttribute( 'src' ), config( 'telegram-webapp.webAppScriptLocation' ), );
        $this->assertEquals( 200, $webAppPageResponse->getStatusCode() );
    }

    #[Test]
    #[DefineEnvironment( 'usesDisabledMode' )]
    public function testWebAppPageLoadsCorrectlyInDisabledMode()
    {
        $webAppPageResponse = $this->get( '/' );
        $dom = $this->loadDOM( $webAppPageResponse->getContent() );
        $webAppScript = $dom->getElementById( "telegram-webapp-script" );
        $pageContentBlock = $dom->getElementById( "app-content" );

        $this->assertNotNull( $pageContentBlock );
        $this->assertNull( $webAppScript );
        $this->assertEquals( 200, $webAppPageResponse->getStatusCode() );
    }

    #[Test]
    #[DefineEnvironment( 'usesDisabledMode' )]
    public function testApiRequestWithDisabledMode()
    {
        $response = $this->post( '/api/telegram-webapp' );
        $this->assertEquals( 200, $response->getStatusCode() );
        $this->assertEquals( [ 'message' => 'OK' ], $response->json() );
    }

    #[Test]
    #[DefineEnvironment( 'useTestTelegramBotToken' )]
    public function testRequestPassedWithInvalidUserDataJson()
    {
        $response = $this->post( '/api/telegram-webapp?query_id=AAE0m7oLAAAAADSbugtKyT4p&user=NOT_JSON&auth_date=1698814911&hash=1e22c77f7ed7c91699d93eaf3925dc7e84a3ebb695642bb6a7664e34df63cc32' );
        $this->assertEquals( 200, $response->getStatusCode() );
        $this->assertEquals( [ 'message' => 'OK' ], $response->json() );
        $this->assertNull( TelegramFacade::getWebAppUser() );
    }

    #[Test]
    #[DefineEnvironment( 'useTestTelegramBotToken' )]
    public function testRequestPassedWithValidTelegramData()
    {
        $response = $this->post( '/api/telegram-webapp?query_id=AAE0m7oLAAAAADSbugtKyT4p&user={"id":111111111,"first_name":"Evgen","last_name":"Evgen","username":"micromagicman","language_code":"xx","is_premium":true,"allows_write_to_pm":true}&auth_date=1698814911&hash=dfe88aa8ef09d25f3de5a4c88dae3058ba480f3fece4dcfda376111617df11da' );
        $this->assertEquals( 200, $response->getStatusCode() );
        $this->assertEquals( [ 'message' => 'OK' ], $response->json() );

        $expectedUser = new TelegramUser( [
            'id' => 111111111,
            'first_name' => 'Evgen',
            'last_name' => 'Evgen',
            'username' => 'micromagicman',
            'language_code' => 'xx',
            'is_premium' => true,
            'allows_write_to_pm' => true,
        ] );

        $this->assertEquals( $expectedUser, TelegramFacade::getWebAppUser() );
        $this->assertEquals( $expectedUser, TelegramFacade::getWebAppUser( request() ) );
    }

    #[Test]
    #[DefineEnvironment( 'useTestTelegramBotToken' )]
    public function testRequestDeclinedWithoutTelegramData()
    {
        $response = $this->post( '/api/telegram-webapp' );
        $this->assertEquals( config( 'telegram-webapp.error.status' ), $response->getStatusCode() );
        $this->assertNull( TelegramFacade::getWebAppUser() );
    }

    private function loadDOM( string $htmlContent ): DOMDocument
    {
        $dom = new DOMDocument();
        $dom->loadHTML( $htmlContent );
        return $dom;
    }
}