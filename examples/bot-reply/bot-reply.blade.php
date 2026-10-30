@extends('telegram-webapp::main')

@section('title', 'Bot Reply')

@section('content')
    <div style="padding: 1rem; font-family: sans-serif;">
        <h2>Bot Reply</h2>
        <button id="notify" type="button">Send me a message</button>
        <p id="result"></p>
    </div>

    <script>
        const tg = window.Telegram.WebApp;
        tg.ready();

        document.getElementById('notify').addEventListener('click', async () => {
            const response = await fetch('/api/bot-reply?' + tg.initData, { method: 'POST' });
            const data = await response.json();
            document.getElementById('result').textContent = data.message;
        });
    </script>
@endsection
