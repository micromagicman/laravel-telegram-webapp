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
            Log::error( "User with Telegram WebAppData {webAppData} is invalid!",
                [ 'webAppData' => $request->query() ] );
            $this->webAppService->abortWithError();
        }
        Log::debug( "User with WebAppData {webAppData} is valid", [ 'webAppData' => $request->query() ] );
        return $next( $request );
    }
}