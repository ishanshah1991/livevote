<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @hasSection('og_title')
        <meta property="og:title" content="@yield('og_title')">
    @endif
    @hasSection('og_description')
        <meta property="og:description" content="@yield('og_description')">
    @endif
    @vite(['resources/css/app.css'])
    @stack('head')
</head>
<body style="margin:0; background:var(--clr-surface-alt); color:var(--clr-text); font-family:system-ui,sans-serif; min-height:100vh; display:flex; flex-direction:column; align-items:center; padding:2.5rem 1rem;">
    <main style="width:100%; max-width:var(--max-w-poll);">
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
