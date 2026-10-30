<?php

use App\Http\Controllers\UserFormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web route — serves the Mini App page
|--------------------------------------------------------------------------
| Copy this into routes/web.php.
*/
Route::get('/form', [UserFormController::class, 'show']);

/*
|--------------------------------------------------------------------------
| API route — receives and validates the form submission
|--------------------------------------------------------------------------
| Copy this into routes/api.php.
*/
Route::post('/form', [UserFormController::class, 'store'])
    ->middleware('telegram-webapp');
