<?php

namespace Micromagicman\TelegramWebApp\Facade;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Micromagicman\TelegramWebApp\Service\TelegramWebAppService;

/**
 * A static proxy to {@link TelegramWebAppService} service
 * @method static getWebAppUser( ?Request $request = null )
 */
class TelegramFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return TelegramWebAppService::class;
    }
}