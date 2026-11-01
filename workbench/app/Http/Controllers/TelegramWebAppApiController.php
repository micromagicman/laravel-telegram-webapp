<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Micromagicman\TelegramWebApp\Dto\TelegramUser;

class TelegramWebAppApiController extends Controller
{
    public function process(): JsonResponse
    {
        return response()->json([
            'message' => 'OK',
        ]);
    }

    public function user(TelegramUser $user): JsonResponse
    {
        return response()->json([
            'id' => $user->getId(),
            'first_name' => $user->getFirstName(),
        ]);
    }
}
