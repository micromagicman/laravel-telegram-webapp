<?php

namespace Micromagicman\TelegramWebApp;

use Illuminate\Support\ServiceProvider;

class WebAppDataValidationProvider extends ServiceProvider
{

    public function boot(): void
    {
        $this->publishes( [
            __DIR__ . '/../config/telegram-webapp.php' => config_path( 'telegram-webapp.php' )
        ] );
        $this->loadRoutesFrom( __DIR__ . '/../routes/telegram-webapp.php' );
    }
}