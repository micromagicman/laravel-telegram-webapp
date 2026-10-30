<?php

use App\Http\Controllers\HelloUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web route — serves the Mini App page
|--------------------------------------------------------------------------
| Copy this into routes/web.php.
*/
Route::get('/hello', [HelloUserController::class, 'show']);

/*
|--------------------------------------------------------------------------
| API route — receives and validates the initData
|--------------------------------------------------------------------------
| Copy this into routes/api.php (the api group has no CSRF middleware,
| which is what you want for a Mini App endpoint).
*/
Route::post('/hello', [HelloUserController::class, 'greet'])
    ->middleware('telegram-webapp');
