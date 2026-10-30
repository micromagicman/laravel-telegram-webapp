@extends('telegram-webapp::main')

@section('title', 'Hello User')

@section('content')
    <div style="padding: 1rem; font-family: sans-serif;">
        <h2>Hello User</h2>
        <button id="greet" type="button">Greet me</button>
        <p id="result"></p>
    </div>

    <script>
        const tg = window.Telegram.WebApp;
        tg.ready(); // tell Telegram the app is ready

        document.getElementById('greet').addEventListener('click', async () => {
            // Send the initData as the query string — the middleware validates it.
            const response = await fetch('/api/hello?' + tg.initData, { method: 'POST' });
            const data = await response.json();
            document.getElementById('result').textContent = data.message;
        });
    </script>
@endsection
