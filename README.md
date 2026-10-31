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

The quickest way to get started is the install command, which publishes the config,
scaffolds a sample controller, route file and blade view, and prints the required
`.env` key:

```bash
php artisan telegram-webapp:install
```

To skip the scaffolding and only publish the config, pass `--no-scaffold`.

You can also publish manually:

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

## Injecting the Telegram user

When a route is protected by the `telegram-webapp` middleware, the validated user is
stored on the request and can be injected directly into a controller method — in the
same style as Laravel's route-model binding:

```php
use Micromagicman\TelegramWebApp\Dto\TelegramUser;

class MyController extends Controller
{
    public function process(Request $request, TelegramUser $user)
    {
        $id = $user->getId();
        // ...
    }
}
```

There is no need to call `telegramUser()` manually. If no valid user is present
(the middleware was not applied, or the `user` field failed to parse), the container
aborts with the configured `error.status` (default `403`) instead of resolving the
dependency.

## Third-party signature validation

For Mini Apps you do **not** own the bot token of, Telegram also signs the initData
with its own Ed25519 key. Validate it via the `signature` field (instead of the
bot-token `hash`) by enabling the feature and, optionally, providing the bot id:

```php
// config/telegram-webapp.php
'signature' => [
    'enabled' => env('TELEGRAM_WEBAPP_SIGNATURE_VALIDATION_ENABLED', false),
    'publicKey' => env('TELEGRAM_WEBAPP_SIGNATURE_PUBLIC_KEY'),
    'botId' => env('TELEGRAM_WEBAPP_SIGNATURE_BOT_ID'),
],
```

- `publicKey` defaults to Telegram's production Ed25519 key; set the test key for
  the test environment.
- `botId` is the numeric id (the part before `:` in the token); when omitted it is
  derived from `botToken`.

When `enabled` is `true`, the existing bot-token `hash` path is bypassed and the
Ed25519 `signature` is verified instead.

## Examples

Ready-to-copy Mini App examples (authentication, forms, bot replies) live in the
[`examples/`](examples) directory — see [`examples/README.md`](examples/README.md) for
setup instructions and a walkthrough of how Mini App authentication works.