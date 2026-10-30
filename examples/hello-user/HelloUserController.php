<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HelloUserController extends Controller
{
    /**
     * Render the Mini App page.
     */
    public function show(): View
    {
        return view('hello-user');
    }

    /**
     * Validate the request and greet the user.
     *
     * The 'telegram-webapp' middleware has already verified the initData,
     * so telegramUser() returns the authenticated user.
     */
    public function greet(): JsonResponse
    {
        $user = telegramUser();

        return response()->json([
            'message' => 'Hello, '.$user->getFirstName().'!',
        ]);
    }
}
