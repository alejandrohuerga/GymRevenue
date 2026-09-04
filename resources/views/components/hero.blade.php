<section class="max-w-6xl mx-auto px-6 pt-16 md:pt-24 pb-16 grid md:grid-cols-12 gap-12 items-center">
    <div class="md:col-span-7">
        <p class="text-sm uppercase tracking-widest text-emerald-400 mb-6">Revenue intelligence for gyms</p>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.05] text-balance">
            Tu gimnasio está perdiendo ingresos.
            <span class="block text-zinc-400">GymRevenue te dice dónde.</span>
        </h1>

        <p class="mt-6 max-w-xl text-zinc-400 leading-relaxed text-pretty">
            Descubre qué socios están en riesgo, qué clientes están inactivos
            y qué oportunidades de recuperación estás dejando pasar.
        </p>

        <div class="mt-8">
            <a
                href="{{ route('calculator') }}"
                class="inline-block rounded-sm bg-emerald-400 px-8 py-4 text-sm font-bold text-zinc-950 hover:bg-emerald-300 transition-colors"
            >
                Analizar mi gimnasio gratis
            </a>
        </div>

        <p class="mt-4 text-sm text-zinc-500">
            Sin cambiar tu software · Análisis inicial gratuito · Resultados en minutos
        </p>
    </div>

    <div class="md:col-span-5">
        <x-dashboard-card />
    </div>
</section>