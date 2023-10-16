<?php

namespace Micromagicman\TelegramWebApp;

use Closure;

class WebAppDataValidationMiddleware
{

    public function handle( $request, Closure $next )
    {
        return $next( $request );
    }
}