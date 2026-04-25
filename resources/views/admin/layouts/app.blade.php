<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · {{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 960px; margin: 3rem auto; padding: 0 1rem; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input { width: 100%; padding: 0.5rem; margin-top: 0.25rem; box-sizing: border-box; }
        button { margin-top: 1.25rem; padding: 0.6rem 1rem; }
        .errors { background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .errors ul { margin: 0; padding-left: 1.25rem; }
        .status { background: #dcfce7; color: #14532d; padding: 0.75rem 1rem; border-radius: 4px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <h1>@yield('heading')</h1>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')

    @stack('scripts')
</body>
</html>
