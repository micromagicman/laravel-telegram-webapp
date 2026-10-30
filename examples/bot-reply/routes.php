<?php

use App\Http\Controllers\BotReplyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web route — serves the Mini App page
|--------------------------------------------------------------------------
| Copy this into routes/web.php.
*/
Route::get('/bot-reply', [BotReplyController::class, 'show']);

/*
|--------------------------------------------------------------------------
| API route — verifies the user and replies via the bot
|--------------------------------------------------------------------------
| Copy this into routes/api.php.
*/
Route::post('/bot-reply', [BotReplyController::class, 'notify'])
    ->middleware('telegram-webapp');
