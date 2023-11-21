<!doctype html>
<html lang="@yield('lang', 'EN')">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @if(config('telegram-webapp.enabled'))
        <script id="telegram-webapp-script" src="{{config('telegram-webapp.webAppScriptLocation')}}"></script>
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('head')
    <title>@yield('title', 'Telegram WebApp')</title>
</head>
<body>
@yield('content')
</body>
</html>
