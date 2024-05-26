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

## Telegram bot API

### answerWebAppQuery

Set the result of an interaction with a Web App and send a corresponding message on behalf of the user to the chat 
from which the query originated. On success, an `Illuminate\Http\Client\Response` object is returned.

#### Parameters

`$result` - one of [InlineQueryResult](https://core.telegram.org/bots/api#inlinequeryresult) variations (php array)

```php

use Micromagicman\TelegramWebApp\Api\TelegramBotApi;

private TelegramBotApi $botApi;

$response = $botApi->answerWebpAppQuery([
    'type' => 'document'
    // ...
    // one of InlineQueryResult variations
    // ...
]);
```