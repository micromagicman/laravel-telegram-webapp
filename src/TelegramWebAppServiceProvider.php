<?php

namespace Micromagicman\TelegramWebApp;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Micromagicman\TelegramWebApp\Http\WebAppDataValidationMiddleware;

class TelegramWebAppServiceProvider extends ServiceProvider
{

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