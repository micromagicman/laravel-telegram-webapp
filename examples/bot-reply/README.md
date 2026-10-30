# Bot Reply

Send a reply back to the user through the Bot API. This example shows the
package's `BotApi` proxy: once the user is verified, you can call any
`TelegramBot\Api\BotApi` method (here `sendMessage`) straight from the
service or facade.

## Files to copy

| File | Destination |
|------|-------------|
| `routes.php` | merge into your `routes/web.php` and `routes/api.php` |
| `BotReplyController.php` | `app/Http/Controllers/BotReplyController.php` |
| `bot-reply.blade.php` | `resources/views/bot-reply.blade.php` |

## What happens

1. The user taps a button in the Mini App.
2. The frontend POSTs `Telegram.WebApp.initData` to your endpoint.
3. The middleware verifies the user, then the controller calls
   `sendMessage()` to drop a message into the user's private chat.

> **Privacy note.** A bot can only message a user who has interacted with it;
> `TelegramUser::isAllowsWriteToPm()` tells you whether the current user
> allowed the bot to write to them. Guard your `sendMessage` calls with it.

## Sending data *to* the bot (`sendData`)

If instead the frontend calls `Telegram.WebApp.sendData(data)`, Telegram
closes the Mini App and delivers the data to the bot as a
`web_app_data` service message. Handling that requires a
[webhook](https://core.telegram.org/bots/api#setwebhook) and
`answerWebAppQuery()` to acknowledge the query — see
[`../README.md`](../README.md) for the webhook note.
