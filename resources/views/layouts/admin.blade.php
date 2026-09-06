<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Panel — GymRevenue')</title>

        <meta name="robots" content="noindex, nofollow">
        <link rel="canonical" href="{{ url()->current() }}">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css'])
        @endif
    </head>
    <body class="bg-zinc-950 text-zinc-100 antialiased min-h-screen flex flex-col">
        <header class="border-b border-zinc-800 bg-zinc-950/95 sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="{{ route('admin.leads.index') }}" class="text-lg font-black tracking-tight shrink-0">
                    GYM<span class="text-emerald-400">REVENUE</span>
                    <span class="ml-2 inline-block rounded-sm border border-emerald-400/40 px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider text-emerald-400 align-middle">Panel</span>
                </a>

                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-sm text-zinc-400 hover:text-zinc-100 transition-colors">Ver web</a>
                    <span class="hidden sm:inline text-sm text-zinc-500">{{ auth()->user()?->email }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="rounded-sm border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-300 hover:border-red-500/50 hover:text-red-400 transition-colors"
                        >
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>
    </body>
</html>