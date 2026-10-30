# User Form

A form the user fills in; the submission is validated on the server, then
stored (or processed) and echoed back.

## Files to copy

| File | Destination |
|------|-------------|
| `routes.php` | merge into your `routes/web.php` and `routes/api.php` |
| `UserFormController.php` | `app/Http/Controllers/UserFormController.php` |
| `user-form.blade.php` | `resources/views/user-form.blade.php` |

## What happens

1. The page renders a simple form.
2. On submit, the frontend POSTs the form data with `Telegram.WebApp.initData`
   as the query string.
3. The `telegram-webapp` middleware verifies the `initData` **before** your
   controller runs, so only genuine Telegram users reach the handler.
4. The controller validates the input, stores it, and returns a confirmation.

> **Note on `sendData`.** `Telegram.WebApp.sendData(data)` is an alternative
> that closes the Mini App and delivers `data` to the bot (see the
> [bot-reply](../bot-reply) example). It is meant for handing data *to the
> bot*, not for round-tripping to your own API. For a normal form you POST to
> your own endpoint, as shown here.
