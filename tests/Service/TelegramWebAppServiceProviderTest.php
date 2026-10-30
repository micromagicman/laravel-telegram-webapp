<?php

namespace Micromagicman\TelegramWebApp\Tests\Service;

use BadMethodCallException;
use DOMDocument;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Micromagicman\TelegramWebApp\Dto\TelegramUser;
use Micromagicman\TelegramWebApp\Facade\TelegramFacade;
use Micromagicman\TelegramWebApp\Service\TelegramWebAppService;
use Micromagicman\TelegramWebApp\TelegramWebAppServiceProvider;
use Micromagicman\TelegramWebApp\Tests\Fixtures\StubBotApi;
use Micromagicman\TelegramWebApp\Util\Crypto;
use Micromagicman\TelegramWebApp\Util\Time;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TelegramWebAppServiceProviderTest extends TestCase
{
    use WithWorkbench;

    protected function useDisabledMode(Application $app): void
    {
        $app['config']->set('telegram-webapp.enabled', false);
    }

    protected function useTestTelegramBotToken(Application $app): void
    {
        $app['config']->set('telegram-webapp.botToken', 'test-token');
    }

    /**
     * @throws Exception
     */
    protected function useCryptoServiceMock(Application $app): void
    {
        $cryptoMock = $this->createMock(Crypto::class);
        $cryptoMock->expects($this->atLeast(2))
            ->method('hmacSHA256')
            ->willReturn('1e22c77f7ed7c91699d93eaf3925dc7e84a3ebb695642bb6a7664e34df63cc32');
        $app->singleton(Crypto::class, fn () => $cryptoMock);
    }

    protected function useAuthDateLifetimeMinute(Application $app): void
    {
        $app['config']->set('telegram-webapp.authDateLifetimeSeconds', 60);
    }

    #[Test]
    public function test_publish_config_in_console_context()
    {
        Artisan::call('vendor:publish', ['--provider' => TelegramWebAppServiceProvider::class]);
        $publishedFilePath = config_path('telegram-webapp.php');
        $this->assertTrue(file_exists($publishedFilePath), 'Config file was not published.');
    }

    #[Test]
    public function test_web_app_page_loads_correctly()
    {
        $webAppPageResponse = $this->get('/');
        $dom = $this->loadDOM($webAppPageResponse->getContent());
        $webAppScript = $dom->getElementById('telegram-webapp-script');
        $pageContentBlock = $dom->getElementById('app-content');

        $this->assertNotNull($pageContentBlock);
        $this->assertNotNull($webAppScript);
        $this->assertEquals($webAppScript->getAttribute('src'), config('telegram-webapp.webAppScriptLocation'));
        $this->assertEquals(200, $webAppPageResponse->getStatusCode());
    }

    #[Test]
    #[DefineEnvironment('useDisabledMode')]
    public function test_web_app_page_loads_correctly_in_disabled_mode()
    {
        $webAppPageResponse = $this->get('/');
        $dom = $this->loadDOM($webAppPageResponse->getContent());
        $webAppScript = $dom->getElementById('telegram-webapp-script');
        $pageContentBlock = $dom->getElementById('app-content');

        $this->assertEquals(200, $webAppPageResponse->getStatusCode());
        $this->assertNotNull($pageContentBlock);
        $this->assertNull($webAppScript);
    }

    #[Test]
    #[DefineEnvironment('useDisabledMode')]
    #[DefineEnvironment('useTestTelegramBotToken')]
    public function test_api_request_with_disabled_mode()
    {
        $response = $this->post('/api/telegram-webapp');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'OK'], $response->json());
    }

    #[Test]
    #[DefineEnvironment('useTestTelegramBotToken')]
    public function test_request_passed_with_invalid_user_data_json()
    {
        $response = $this->post('/api/telegram-webapp?query_id=AAE0m7oLAAAAADSbugtKyT4p&user=NOT_JSON&auth_date=1698814911&hash=1e22c77f7ed7c91699d93eaf3925dc7e84a3ebb695642bb6a7664e34df63cc32');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'OK'], $response->json());
        $this->assertNull(TelegramFacade::getWebAppUser());
    }

    #[Test]
    #[DefineEnvironment('useTestTelegramBotToken')]
    public function test_request_passed_with_valid_telegram_data()
    {
        $response = $this->post('/api/telegram-webapp?query_id=AAE0m7oLAAAAADSbugtKyT4p&user={"id":111111111,"first_name":"Evgen","last_name":"Evgen","username":"micromagicman","language_code":"xx","is_premium":true,"allows_write_to_pm":true}&auth_date=1698814911&hash=dfe88aa8ef09d25f3de5a4c88dae3058ba480f3fece4dcfda376111617df11da');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'OK'], $response->json());

        $expectedUser = new TelegramUser([
            'id' => 111111111,
            'first_name' => 'Evgen',
            'last_name' => 'Evgen',
            'username' => 'micromagicman',
            'language_code' => 'xx',
            'is_premium' => true,
            'allows_write_to_pm' => true,
        ]);

        $this->assertEquals($expectedUser, TelegramFacade::getWebAppUser());
        $this->assertEquals($expectedUser, TelegramFacade::getWebAppUser(request()));
    }

    #[Test]
    #[DefineEnvironment('useTestTelegramBotToken')]
    public function test_request_declined_without_telegram_data()
    {
        $response = $this->post('/api/telegram-webapp');
        $this->assertEquals(config('telegram-webapp.error.status'), $response->getStatusCode());
        $this->assertNull(TelegramFacade::getWebAppUser());
    }

    #[Test]
    #[DefineEnvironment('useCryptoServiceMock')]
    #[DefineEnvironment('useTestTelegramBotToken')]
    #[DefineEnvironment('useAuthDateLifetimeMinute')]
    public function test_request_passed_with_valid_auth_date()
    {
        $authDate = time() - 55;
        $response = $this->post("/api/telegram-webapp?query_id=AAE0m7oLAAAAADSbugtKyT4p&user={\"id\":111111111,\"first_name\":\"Evgen\",\"last_name\":\"Evgen\",\"username\":\"micromagicman\",\"language_code\":\"xx\",\"is_premium\":true,\"allows_write_to_pm\":true}&auth_date=$authDate&hash=1e22c77f7ed7c91699d93eaf3925dc7e84a3ebb695642bb6a7664e34df63cc32");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'OK'], $response->json());
        $expectedUser = new TelegramUser([
            'id' => 111111111,
            'first_name' => 'Evgen',
            'last_name' => 'Evgen',
            'username' => 'micromagicman',
            'language_code' => 'xx',
            'is_premium' => true,
            'allows_write_to_pm' => true,
        ]);

        $this->assertEquals($expectedUser, TelegramFacade::getWebAppUser());
        $this->assertEquals($expectedUser, TelegramFacade::getWebAppUser(request()));
    }

    #[Test]
    #[DefineEnvironment('useTestTelegramBotToken')]
    #[DefineEnvironment('useAuthDateLifetimeMinute')]
    public function test_request_passed_with_invalid_auth_date()
    {
        $authDate = time() - 61;
        $response = $this->post("/api/telegram-webapp?query_id=AAE0m7oLAAAAADSbugtKyT4p&user={\"id\":111111111,\"first_name\":\"Evgen\",\"last_name\":\"Evgen\",\"username\":\"micromagicman\",\"language_code\":\"xx\",\"is_premium\":true,\"allows_write_to_pm\":true}&auth_date=$authDate&hash=1e22c77f7ed7c91699d93eaf3925dc7e84a3ebb695642bb6a7664e34df63cc32");
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Test]
    #[DefineEnvironment('useTestTelegramBotToken')]
    public function test_telegram_bot_api_proxy_with_existing_method()
    {
        $mockApi = $this->getMockBuilder(StubBotApi::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMe'])
            ->getMock();
        $mockApi->expects($this->once())
            ->method('getMe')
            ->willReturn(['ok' => true]);
        $service = new TelegramWebAppService($mockApi, new Crypto, new Time);
        $result = $service->getMe();
        $this->assertEquals(['ok' => true], $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Test]
    #[DefineEnvironment('useTestTelegramBotToken')]
    public function test_telegram_bot_api_proxy_with_not_existing_method()
    {
        $mockApi = $this->createStub(StubBotApi::class);
        $service = new TelegramWebAppService($mockApi, new Crypto, new Time);
        $this->assertThrows(
            fn () => $service->notExists(),
            BadMethodCallException::class,
            'Method notExists does not exists in Telegram bot api'
        );
    }

    #[Test]
    #[DefineEnvironment('useTestTelegramBotToken')]
    public function test_get_web_app_data_maps_all_fields()
    {
        $request = Request::create('/api/telegram-webapp', 'GET', [
            'query_id' => 'AAE0m7oLAAAAADSbugtKyT4p',
            'user' => json_encode(['id' => 111111111, 'first_name' => 'Evgen']),
            'receiver' => json_encode(['id' => 222222222, 'first_name' => 'Other']),
            'chat' => json_encode(['id' => 333333333, 'type' => 'group', 'title' => 'Test']),
            'chat_type' => 'group',
            'chat_instance' => '444444444',
            'start_param' => 'ref123',
            'can_send_after' => '1698815000',
            'auth_date' => '1698814911',
            'signature' => 'abc123',
        ]);

        $service = new TelegramWebAppService(new StubBotApi, new Crypto, new Time);
        $data = $service->getWebAppData($request);

        $this->assertSame('AAE0m7oLAAAAADSbugtKyT4p', $data->getQueryId());
        $this->assertSame(111111111, $data->getUser()->getId());
        $this->assertSame(222222222, $data->getReceiver()->getId());
        $this->assertSame(333333333, $data->getChat()->getId());
        $this->assertSame('group', $data->getChatType());
        $this->assertSame('444444444', $data->getChatInstance());
        $this->assertSame('ref123', $data->getStartParam());
        $this->assertSame(1698815000, $data->getCanSendAfter());
        $this->assertSame(1698814911, $data->getAuthDate());
        $this->assertSame('abc123', $data->getSignature());
    }

    private function loadDOM(string $htmlContent): DOMDocument
    {
        $dom = new DOMDocument;
        $dom->loadHTML($htmlContent);

        return $dom;
    }
}
