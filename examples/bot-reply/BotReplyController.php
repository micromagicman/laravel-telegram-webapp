<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Micromagicman\TelegramWebApp\Service\TelegramWebAppService;

class BotReplyController extends Controller
{
    public function __construct(
        private readonly TelegramWebAppService $telegram
    ) {}

    /**
     * Render the Mini App page.
     */
    public function show(): View
    {
        return view('bot-reply');
    }

    /**
     * Verify the user (middleware) and send them a message via the bot.
     */
    public function notify(): JsonResponse
    {
        $user = telegramUser();

        // The service proxies TelegramBot\Api\BotApi methods.
        if ($user->isAllowsWriteToPm()) {
            $this->telegram->sendMessage($user->getId(), 'Thanks for using the Mini App!');
        }

        return response()->json([
            'message' => 'Check your private chat!',
        ]);
    }
}
