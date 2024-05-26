<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\TelegramWebAppApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix( '/api' )
    ->middleware( 'telegram-webapp' )
    ->group( function () {
        Route::post( '/telegram-webapp', [ TelegramWebAppApiController::class, 'process' ] );
    } );


