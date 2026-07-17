<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TG Agro') }} — @yield('title', 'Connexion')</title>

    @vite(['resources/css/app.css'])
    <link href="{{ asset('css/tokens.css') }}" rel="stylesheet">
    <script src="/alpine/alpine.js" defer></script>

    @stack('styles')
</head>
<body class="min-h-screen antialiased overflow-x-hidden">
    @yield('content')
    @stack('scripts')
</body>
</html>
