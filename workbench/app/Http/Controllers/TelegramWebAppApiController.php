<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class TelegramWebAppApiController extends Controller
{

    public function process(): JsonResponse
    {
        return response()->json( [
            'message' => 'OK'
        ] );
    }
}