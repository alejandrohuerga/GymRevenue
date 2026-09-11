<header class="border-b border-zinc-800 bg-zinc-950/95 backdrop-blur sticky top-0 z-50">
    <nav class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="Principal">
        <a href="{{ route('home') }}" class="text-lg font-black tracking-tight">
            GYM<span class="text-emerald-400">REVENUE</span>
        </a>

        <div class="hidden md:flex items-center gap-8 text-sm text-zinc-400">
            <a href="{{ route('how-it-works') }}" class="hover:text-zinc-100 transition-colors">Cómo funciona</a>
            <a href="{{ route('calculator') }}" class="hover:text-zinc-100 transition-colors">Calculadora</a>
            <a href="{{ route('pricing') }}" class="hover:text-zinc-100 transition-colors">Precios</a>
        </div>

        <div class="hidden md:block">
            <a
                href="{{ route('calculator') }}"
                data-track-cta="navbar"
                class="rounded-sm bg-emerald-400 px-5 py-2.5 text-sm font-bold text-zinc-950 hover:bg-emerald-300 transition-colors"
            >
                Analizar mi gimnasio
            </a>
        </div>

        <div class="flex items-center gap-3 md:hidden">
            <a
                href="{{ route('calculator') }}"
                data-track-cta="navbar-mobile"
                class="rounded-sm bg-emerald-400 px-4 py-2 text-xs font-bold text-zinc-950 hover:bg-emerald-300 transition-colors"
            >
                Analizar
            </a>
            <button
                type="button"
                data-menu-toggle
                aria-controls="mobile-menu"
                aria-expanded="false"
                class="flex flex-col items-end justify-center gap-1.5 p-2"
            >
                <span class="block h-0.5 w-6 bg-zinc-100"></span>
                <span class="block h-0.5 w-4 bg-emerald-400"></span>
                <span class="block h-0.5 w-6 bg-zinc-100"></span>
                <span class="sr-only">Abrir menú</span>
            </button>
        </div>
    </nav>

    <div id="mobile-menu" data-menu hidden class="md:hidden border-t border-zinc-800 px-6 py-6">
        <div class="grid gap-4 text-sm text-zinc-400">
            <a href="{{ route('how-it-works') }}" class="hover:text-zinc-100 transition-colors">Cómo funciona</a>
            <a href="{{ route('calculator') }}" class="hover:text-zinc-100 transition-colors">Calculadora</a>
            <a href="{{ route('pricing') }}" class="hover:text-zinc-100 transition-colors">Precios</a>
        </div>
        <a
            href="{{ route('calculator') }}"
            data-track-cta="navbar-mobile"
            class="mt-6 inline-block rounded-sm bg-emerald-400 px-5 py-3 text-sm font-bold text-zinc-950 hover:bg-emerald-300 transition-colors"
        >
            Analizar mi gimnasio
        </a>
    </div>
</header>