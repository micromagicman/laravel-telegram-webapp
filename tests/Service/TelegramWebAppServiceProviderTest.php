<?php

namespace Micromagicman\TelegramWebApp\Tests\Service;

use BadMethodCallException;
use DOMDocument;
use Illuminate\Foundation\Application;
use Micromagicman\TelegramWebApp\Tests\Fixtures\StubBotApi;
use Micromagicman\TelegramWebApp\Dto\TelegramUser;
use Micromagicman\TelegramWebApp\Facade\TelegramFacade;
use Micromagicman\TelegramWebApp\Service\TelegramWebAppService;
use Micromagicman\TelegramWebApp\Util\Crypto;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;

class TelegramWebAppServiceProviderTest extends TestCase
{
    use WithWorkbench;

    protected function useDisabledMode( Application $app ): void
    {
        $app[ 'config' ]->set( 'telegram-webapp.enabled', false );
    }

    protected function useTestTelegramBotToken( Application $app ): void
    {
        $app[ 'config' ]->set( 'telegram-webapp.botToken', 'test-token' );
    }

    /**
     * @throws Exception
     */
    protected function useCryptoServiceMock( Application $app ): void
    {
        $cryptoMock = $this->createMock( Crypto::class );
        $cryptoMock->expects( $this->atLeast( 2 ) )
            ->method( 'hmacSHA256' )
            ->willReturn( '1e22c77f7ed7c91699d93eaf3925dc7e84a3ebb695642bb6a7664e34df63cc32' );
        $app->singleton( Crypto::class, fn() => $cryptoMock );
    }

    protected function useAuthDateLifetimeMinute( Application $app ): void
    {
        $app[ 'config' ]->set( 'telegram-webapp.authDateLifetimeSeconds', 60 );
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
    #[DefineEnvironment( 'useDisabledMode' )]
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
    #[DefineEnvironment( 'useDisabledMode' )]
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

    #[Test]
    #[DefineEnvironment( 'useCryptoServiceMock' )]
    #[DefineEnvironment( 'useTestTelegramBotToken' )]
    #[DefineEnvironment( 'useAuthDateLifetimeMinute' )]
    public function testRequestPassedWithValidAuthDate()
    {
        $authDate = time() - 55;
        $response = $this->post( "/api/telegram-webapp?query_id=AAE0m7oLAAAAADSbugtKyT4p&user={\"id\":111111111,\"first_name\":\"Evgen\",\"last_name\":\"Evgen\",\"username\":\"micromagicman\",\"language_code\":\"xx\",\"is_premium\":true,\"allows_write_to_pm\":true}&auth_date=$authDate&hash=1e22c77f7ed7c91699d93eaf3925dc7e84a3ebb695642bb6a7664e34df63cc32" );
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
    #[DefineEnvironment( 'useAuthDateLifetimeMinute' )]
    public function testRequestPassedWithInvalidAuthDate()
    {
        $authDate = time() - 61;
        $response = $this->post( "/api/telegram-webapp?query_id=AAE0m7oLAAAAADSbugtKyT4p&user={\"id\":111111111,\"first_name\":\"Evgen\",\"last_name\":\"Evgen\",\"username\":\"micromagicman\",\"language_code\":\"xx\",\"is_premium\":true,\"allows_write_to_pm\":true}&auth_date=$authDate&hash=1e22c77f7ed7c91699d93eaf3925dc7e84a3ebb695642bb6a7664e34df63cc32" );
        $this->assertEquals( 403, $response->getStatusCode() );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Test]
    #[DefineEnvironment( 'useTestTelegramBotToken' )]
    public function testTelegramBotApiProxyWithExistingMethod()
    {
        $mockApi = $this->getMockBuilder( StubBotApi::class )
            ->disableOriginalConstructor()
            ->onlyMethods( [ 'getMe' ] )
            ->getMock();
        $mockApi->expects( $this->once() )
            ->method( 'getMe' )
            ->willReturn( [ 'ok' => true ] );
        $service = $this->app->get( TelegramWebAppService::class );
        $reflection = new ReflectionClass( TelegramWebAppService::class );
        $property = $reflection->getProperty( 'telegramBotApi' );
        $property->setAccessible( true );
        $property->setValue( $service, $mockApi );
        $result = $service->getMe();
        $this->assertEquals( [ 'ok' => true ], $result );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Test]
    #[DefineEnvironment( 'useTestTelegramBotToken' )]
    public function testTelegramBotApiProxyWithNotExistingMethod()
    {
        $mockApi = $this->createMock( StubBotApi::class );
        $service = $this->app->get( TelegramWebAppService::class );
        $reflection = new ReflectionClass( TelegramWebAppService::class );
        $property = $reflection->getProperty( 'telegramBotApi' );
        $property->setAccessible( true );
        $property->setValue( $service, $mockApi );
        $this->assertThrows(
            fn() => $service->notExists(),
            BadMethodCallException::class,
            "Method notExists does not exists in Telegram bot api"
        );
    }

    private function loadDOM( string $htmlContent ): DOMDocument
    {
        $dom = new DOMDocument();
        $dom->loadHTML( $htmlContent );
        return $dom;
    }
}