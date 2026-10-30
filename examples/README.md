# Examples

Ready-to-copy examples showing how to build a Telegram Mini App with
`micromagicman/laravel-telegram-webapp`. Each example lives in its own
directory with a short `README.md` and the files you drop into a Laravel app.

## Prerequisites

1. A Telegram bot (create it via [@BotFather](https://t.me/BotFather)).
2. A Laravel app with the package installed:

   ```bash
   composer require micromagicman/laravel-telegram-webapp
   php artisan vendor:publish --provider="Micromagicman\TelegramWebApp\TelegramWebAppServiceProvider"
   ```

3. Your bot token in `.env`:

   ```dotenv
   TELEGRAM_BOT_TOKEN=123456:ABC-DEF...
   ```

## How a Mini App authenticates

A Telegram Mini App is just a web page opened inside Telegram. Telegram
injects the current user's `initData` into the page through the
[WebApp script](https://telegram.org/js/telegram-web-app.js). Your frontend
sends that `initData` to your backend, and this package verifies it (an HMAC
over your bot token) and hands you the `TelegramUser`.

```
Frontend  Telegram.WebApp.initData
    │
    │   POST /your-endpoint?<initData>
    ▼
'telegram-webapp' middleware  ── verifyInitData()
    │
    ▼
telegramUser()  ──  TelegramUser
```

The `initData` must be sent **as the query string** of the request — that is
what the middleware validates. The examples below all do `fetch('/api/...?' +
Telegram.WebApp.initData)`.

## Do I need a webhook?

No. The Mini App talks to **your web endpoint** directly, like a regular SPA
plus API: the `telegram-webapp` middleware validates the `initData` on each
request. A [webhook](https://core.telegram.org/bots/api#setwebhook) is only
needed if the bot must also react to messages or commands sent to it outside
the Mini App (e.g. `answerWebAppQuery` or push notifications via
`sendMessage`).

## Examples

| Example | What it shows |
|---------|---------------|
| [hello-user](hello-user) | Verify the user and greet them by name |
| [user-form](user-form) | A form whose submission is validated on the server |
| [bot-reply](bot-reply) | Reply back to the user through the Bot API |

## Telegraph

[defstudio/telegraph](https://github.com/defstudio/telegraph) manages bots and
webhooks; it is **not required** by this package. You can use both side by
side: this package for Mini App authentication, Telegraph for the rest of your
bot logic.
