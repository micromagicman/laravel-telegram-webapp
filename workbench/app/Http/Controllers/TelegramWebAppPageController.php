<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class TelegramWebAppPageController extends Controller
{

    public function index(): View
    {
        return view( 'main' );
    }
}