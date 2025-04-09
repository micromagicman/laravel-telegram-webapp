# Laravel Telegram WebApp package

![build](https://github.com/micromagicman/laravel-telegram-webapp/actions/workflows/laravel-telegram-webapp-ci.yml/badge.svg)
[![codecov](https://codecov.io/github/micromagicman/laravel-telegram-webapp/graph/badge.svg?token=ZSVF7MGB38)](https://codecov.io/github/micromagicman/laravel-telegram-webapp)

Laravel package that allows you to process commands from Telegram MiniApp with user verification according to
[Telegram MiniApp developer documentation](https://core.telegram.org/bots/webapps), as well as obtaining information
about the Telegram user who sent the request

## Requirements

| Laravel | micromagicman/laravel-telegram-webapp |
|---------|---------------------------------------|
| 10.x    | 1.x.x                                 |
| 11.x    | 2.x.x                                 |
| 12.x    | 3.x.x                                 |

## Install

### Via composer

```bash
composer require micromagicman/laravel-telegram-webapp
```

### Publishing

Publish to your Laravel application:

```bash
php artisan vendor:publish --provider="Micromagicman\TelegramWebApp\TelegramWebAppServiceProvider"
```

## Configure

All package configuration available in `config/telegram-webapp.php` file after `publish` command execution:

| Config name               | Description                                                                                                                                                                                                                                                                                                                                                         | Environment                               | Default value                                 |
|---------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------|-----------------------------------------------|
| `enabled`                 | Telegram MiniApp data validation switch                                                                                                                                                                                                                                                                                                                             | `TELEGRAM_WEBAPP_DATA_VALIDATION_ENABLED` | `true`                                        |
| `webAppScriptLocation`    | Path to script (.js) which initializes Telegram MiniApp on your frontend app                                                                                                                                                                                                                                                                                        | -                                         | `https://telegram.org/js/telegram-web-app.js` |
| `botToken`                | Your Telegram bot token                                                                                                                                                                                                                                                                                                                                             | `TELEGRAM_BOT_TOKEN`                      | -                                             |
| `error.status`            | HTTP status code when Telegram MiniApp data validation fails                                                                                                                                                                                                                                                                                                        | -                                         | 403 (Forbidden)                               |
| `error.message`           | Error message returned when Telegram MiniApp data validation fails                                                                                                                                                                                                                                                                                                  | -                                         | 403 (Forbidden)                               |
| `authDateLifetimeSeconds` | The lifetime of the Telegram initData auth_date parameter in seconds. The request to the server must be made within this interval, otherwise the data transmitted from Telegram will be considered invalid. The values of the parameter <= 0 imply that there is no verification of the lifetime of data from telegram and the auth_date parameter is not validated | -                                         | 0                                             |

Example in code:

## View

This package provides a root view for Telegram MiniApp frontend applications.
[Telegram WebApp script](https://telegram.org/js/telegram-web-app.js) is automatically includes to this view or its
inheritors if `telegram-webapp.enabled` switch is `true`

Example:

```php
@extends('telegram-webapp::main')

@section('lang', 'CN')

@section('head')
// some scripts, css, meta
@endsection

@section('title', 'My title')

@section('content')
    <div id="app-content">
        // My spa content
    </div>
@endsection
```

## Integration with `TelegramBot\Api\BotApi`

Our service integrates with `TelegramBot\Api\BotApi`, allowing you to access all of the methods provided by the Telegram Bot API. This integration is available either through a **Facade** or directly through the service.

You can find the repository for `TelegramBot\Api\BotApi` [here](https://github.com/TelegramBot/Api).

### Using the Facade

To use the Telegram Bot API methods, you can leverage the **TelegramWebAppFacade** facade. This provides a simple and convenient way to interact with the Telegram Bot API.

Example usage with the Facade:

```php
use Micromagicman\TelegramWebApp\Facades\TelegramWebApp;

$response = TelegramWebApp::getMe();
```

This allows you to call methods like `getMe()`, `sendMessage()`, `getUpdates()`, and any other method from the `BotApi` class directly through the facade.

### Using the Service Directly

You can also interact with the Telegram Bot API directly through the service. Inject the `TelegramWebAppService` into your components, and call the Bot API methods via the service instance.

Example usage in a controller:

```php
use Micromagicman\TelegramWebApp\Services\TelegramWebAppService;

class MyController extends Controller
{
    protected $telegram;

    public function __construct(TelegramWebAppService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function getBotInfo()
    {
        $response = $this->telegram->getMe();
        return response()->json($response);
    }
}
```

Both the facade and the service offer full access to the `BotApi` class methods, 
allowing you to work seamlessly with the Telegram Bot API in your Laravel application.