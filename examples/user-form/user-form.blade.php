@extends('telegram-webapp::main')

@section('title', 'User Form')

@section('content')
    <div style="padding: 1rem; font-family: sans-serif;">
        <h2>User Form</h2>
        <form id="note-form">
            <label for="note">Note</label>
            <input id="note" name="note" type="text" placeholder="Type something..." required>
            <button type="submit">Save</button>
        </form>
        <p id="result"></p>
    </div>

    <script>
        const tg = window.Telegram.WebApp;
        tg.ready();

        document.getElementById('note-form').addEventListener('submit', async (event) => {
            event.preventDefault();

            const note = document.getElementById('note').value;
            const body = new URLSearchParams({ note });

            // initData goes in the query string; the form fields go in the body.
            const response = await fetch('/api/form?' + tg.initData, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body,
            });

            const data = await response.json();
            document.getElementById('result').textContent =
                response.ok ? data.message : (data.message || 'Something went wrong');
        });
    </script>
@endsection
