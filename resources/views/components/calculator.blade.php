<section id="calculadora" class="max-w-6xl mx-auto px-6 py-16 md:py-24 scroll-mt-20">
    <div class="max-w-2xl">
        <p class="text-sm uppercase tracking-widest text-emerald-400 mb-4">Calculadora</p>
        <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight text-balance">
            ¿Cuánto dinero podría estar dejando escapar tu gimnasio?
        </h2>
        <p class="mt-4 text-zinc-400 leading-relaxed">
            Introduce unos pocos datos y obtén una estimación de tu oportunidad de recuperación.
        </p>
    </div>

    <div class="mt-10 border border-zinc-800 p-8 md:p-10">
        <form data-calculator-form class="grid md:grid-cols-2 gap-x-8 gap-y-6">
            @csrf

            <div class="grid gap-2">
                <label for="members" class="text-sm text-zinc-400">Número de socios</label>
                <input
                    id="members"
                    name="members"
                    type="number"
                    inputmode="numeric"
                    min="1"
                    max="100000"
                    step="1"
                    value="{{ old('members', 350) }}"
                    placeholder="350"
                    class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                >
                <p data-error-for="members" class="hidden text-sm text-red-400" aria-live="polite"></p>
            </div>

            <div class="grid gap-2">
                <label for="average-fee" class="text-sm text-zinc-400">Cuota mensual media</label>
                <input
                    id="average-fee"
                    name="average_fee"
                    type="number"
                    inputmode="decimal"
                    min="0.01"
                    max="10000"
                    step="0.01"
                    value="{{ old('average_fee', 40) }}"
                    placeholder="40"
                    class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                >
                <p data-error-for="average_fee" class="hidden text-sm text-red-400" aria-live="polite"></p>
            </div>

            <div class="grid gap-2">
                <label for="inactive-members" class="text-sm text-zinc-400">Socios actualmente inactivos</label>
                <input
                    id="inactive-members"
                    name="inactive_members"
                    type="number"
                    inputmode="numeric"
                    min="0"
                    max="100000"
                    step="1"
                    value="{{ old('inactive_members', 30) }}"
                    placeholder="30"
                    class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                >
                <p data-error-for="inactive_members" class="hidden text-sm text-red-400" aria-live="polite"></p>
            </div>

            <div class="grid gap-2">
                <label for="monthly-cancellations" class="text-sm text-zinc-400">Bajas mensuales aproximadas</label>
                <input
                    id="monthly-cancellations"
                    name="monthly_cancellations"
                    type="number"
                    inputmode="numeric"
                    min="0"
                    max="100000"
                    step="1"
                    value="{{ old('monthly_cancellations', 15) }}"
                    placeholder="15"
                    class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                >
                <p data-error-for="monthly_cancellations" class="hidden text-sm text-red-400" aria-live="polite"></p>
            </div>

            <div class="md:col-span-2 pt-2">
                <button
                    type="button"
                    data-calculator-submit
                    class="rounded-sm bg-emerald-400 px-8 py-4 text-sm font-bold text-zinc-950 hover:bg-emerald-300 transition-colors"
                >
                    Calcular mi oportunidad
                </button>
            </div>
        </form>

        <x-calculator-result />
    </div>
</section>