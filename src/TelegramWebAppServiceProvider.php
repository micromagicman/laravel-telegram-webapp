<?php

namespace Micromagicman\TelegramWebApp;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Micromagicman\TelegramWebApp\Api\TelegramApi;
use Micromagicman\TelegramWebApp\Api\TelegramBotApi;
use Micromagicman\TelegramWebApp\Http\WebAppDataValidationMiddleware;

class TelegramWebAppServiceProvider extends ServiceProvider
{

    /**
     * Package singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        TelegramApi::class => TelegramBotApi::class
    ];

    public function boot( Router $router ): void
    {
        $this->publishes( [
            __DIR__ . '/../config/telegram-webapp.php' => config_path( 'telegram-webapp.php' )
        ] );
        $this->loadViewsFrom( __DIR__ . '/../resources/views', 'telegram-webapp' );
        $router->aliasMiddleware( 'telegram-webapp', WebAppDataValidationMiddleware::class );
    }

    public function register(): void
    {
        $this->mergeConfigFrom( __DIR__ . '/../config/telegram-webapp.php', 'telegram-webapp' );
    }
}