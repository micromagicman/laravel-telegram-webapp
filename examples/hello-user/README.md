# Hello User

Minimal Mini App: verify the current user and greet them by first name.

## Files to copy

| File | Destination |
|------|-------------|
| `routes.php` | merge into your `routes/web.php` and `routes/api.php` |
| `HelloUserController.php` | `app/Http/Controllers/HelloUserController.php` |
| `hello-user.blade.php` | `resources/views/hello-user.blade.php` |

Then open the Mini App from a bot button (see the top-level
[`examples/README.md`](../README.md)).

## What happens

1. The page renders via `telegram-webapp::main`, which injects the Telegram
   WebApp script.
2. On click, the frontend sends `Telegram.WebApp.initData` **as the query
   string** to `POST /api/hello`.
3. The `telegram-webapp` middleware verifies the `initData`.
4. `telegramUser()` returns the authenticated `TelegramUser`, and the
   controller greets them by name.
