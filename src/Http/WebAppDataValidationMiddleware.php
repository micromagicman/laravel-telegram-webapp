<?php

namespace Micromagicman\TelegramWebApp\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Micromagicman\TelegramWebApp\Service\TelegramWebAppService;

class WebAppDataValidationMiddleware
{
    public function __construct(
        private readonly TelegramWebAppService $webAppService ) {}

    public function handle( Request $request, Closure $next )
    {
        $enabled = config( 'telegram-webapp.enabled' );
        if ( $enabled && !$this->webAppService->verifyInitData( $request ) ) {
            Log::error(
                'Telegram WebApp User is invalid!',
                [ $request->query() ]
            );
            $this->webAppService->abortWithError();
        }
        return $next( $request );
    }
}