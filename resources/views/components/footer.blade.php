<footer class="border-t border-zinc-800">
    <div class="max-w-6xl mx-auto px-6 py-12 grid gap-10 sm:grid-cols-2 md:grid-cols-4">
        <div>
            <p class="text-lg font-black tracking-tight">GYM<span class="text-emerald-400">REVENUE</span></p>
            <p class="mt-3 text-sm text-zinc-500">Revenue intelligence for gyms.</p>
        </div>

        <div>
            <p class="text-xs uppercase tracking-widest text-zinc-500">Producto</p>
            <ul class="mt-4 grid gap-2 text-sm text-zinc-400">
                <li><a href="{{ route('how-it-works') }}" class="hover:text-zinc-100 transition-colors">Cómo funciona</a></li>
                <li><a href="{{ route('calculator') }}" class="hover:text-zinc-100 transition-colors">Calculadora</a></li>
                <li><a href="{{ route('pricing') }}" class="hover:text-zinc-100 transition-colors">Precios</a></li>
            </ul>
        </div>

        <div>
            <p class="text-xs uppercase tracking-widest text-zinc-500">Recursos</p>
            <ul class="mt-4 grid gap-2 text-sm text-zinc-400">
                <li><span class="text-zinc-600">Blog — próximamente</span></li>
            </ul>
            <p class="mt-6 text-xs uppercase tracking-widest text-zinc-500">Empresa</p>
            <ul class="mt-4 grid gap-2 text-sm text-zinc-400">
                <li><a href="{{ route('home') }}#lead-form" class="hover:text-zinc-100 transition-colors">Contacto</a></li>
            </ul>
        </div>

        <div>
            <p class="text-xs uppercase tracking-widest text-zinc-500">Legal</p>
            <ul class="mt-4 grid gap-2 text-sm text-zinc-400">
                <li><a href="{{ route('legal') }}" class="hover:text-zinc-100 transition-colors">Aviso legal</a></li>
                <li><a href="{{ route('privacy') }}" class="hover:text-zinc-100 transition-colors">Privacidad</a></li>
                <li><a href="{{ route('cookies') }}" class="hover:text-zinc-100 transition-colors">Cookies</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-zinc-800">
        <div class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between text-sm text-zinc-500">
            <p>© 2026 GymRevenue</p>
            <p>Sin ruido. Solo números.</p>
        </div>
    </div>
</footer>