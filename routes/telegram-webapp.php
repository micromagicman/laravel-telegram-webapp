<?php

use Illuminate\Support\Facades\Route;
use Micromagicman\TelegramWebApp\WebAppDataValidationMiddleware;

Route::group( [ 'middleware' => WebAppDataValidationMiddleware::class ], function (): void {

} );