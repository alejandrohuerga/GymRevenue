<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'GymRevenue'))</title>

        @hasSection('meta_description')
            <meta name="description" content="@yield('meta_description')">
        @endif

        <link rel="canonical" href="{{ url()->current() }}">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="GymRevenue">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@yield('title', config('app.name', 'GymRevenue'))">
        @hasSection('meta_description')
            <meta property="og:description" content="@yield('meta_description')">
        @endif

        @stack('head')

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-zinc-950 text-zinc-100 antialiased min-h-screen flex flex-col">
        <x-navbar />

        <main class="flex-1">
            @yield('content')
        </main>

        <x-footer />
    </body>
</html>