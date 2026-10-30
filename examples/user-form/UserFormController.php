<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class UserFormController extends Controller
{
    /**
     * Render the Mini App page.
     */
    public function show(): View
    {
        return view('user-form');
    }

    /**
     * Validate and store the submitted form.
     *
     * The 'telegram-webapp' middleware has already verified the initData.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:255'],
        ]);

        $user = telegramUser();

        // Persist the note however your app needs to, e.g.:
        // Note::create([...'user_id' => $user->getId()...]);

        return response()->json([
            'message' => 'Saved "'.$validated['note'].'" for '.$user->getFirstName().'.',
        ]);
    }
}
