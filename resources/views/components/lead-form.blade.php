<section id="lead-form" class="border-t border-zinc-800 scroll-mt-20">
    <div class="max-w-6xl mx-auto px-6 py-16 md:py-24 grid md:grid-cols-12 gap-12">
        <div class="md:col-span-5">
            <p class="text-sm uppercase tracking-widest text-emerald-400 mb-4">Tu análisis</p>
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight text-balance">
                Descubre exactamente dónde está la oportunidad.
            </h2>
            <p class="mt-4 text-zinc-400 leading-relaxed">
                Déjanos los datos básicos de tu gimnasio y te mostraremos
                las oportunidades que podrías estar dejando pasar.
            </p>
        </div>

        <div class="md:col-span-7">
            <form data-lead-form action="{{ route('lead.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-6">
                @csrf

                <div aria-hidden="true" class="hidden">
                    <label for="website">No rellenar este campo</label>
                    <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid gap-2">
                    <label for="contact-name" class="text-sm text-zinc-400">Tu nombre</label>
                    <input
                        id="contact-name"
                        name="contact_name"
                        type="text"
                        value="{{ old('contact_name') }}"
                        required
                        maxlength="255"
                        class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                        placeholder="Tu nombre"
                    >
                    @error('contact_name')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-2">
                    <label for="email" class="text-sm text-zinc-400">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        maxlength="255"
                        class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                        placeholder="tunombre@gimnasio.com"
                    >
                    @error('email')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-2">
                    <label for="gym-name" class="text-sm text-zinc-400">Nombre del gimnasio</label>
                    <input
                        id="gym-name"
                        name="gym_name"
                        type="text"
                        value="{{ old('gym_name') }}"
                        required
                        maxlength="255"
                        class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                        placeholder="Nombre de tu gimnasio"
                    >
                    @error('gym_name')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-2">
                    <label for="software" class="text-sm text-zinc-400">Software que utilizas <span class="text-zinc-600">(opcional)</span></label>
                    <input
                        id="software"
                        name="software"
                        type="text"
                        value="{{ old('software') }}"
                        maxlength="255"
                        class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                        placeholder="Ej.: Excel, o tu software actual"
                    >
                    @error('software')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-2">
                    <label for="members-csv" class="text-sm text-zinc-400">
                        ¿Tienes un CSV con los datos de tus socios? <span class="text-zinc-600">(opcional)</span>
                    </label>
                    <input
                        id="members-csv"
                        name="csv"
                        type="file"
                        accept=".csv,text/csv,text/plain"
                        class="block w-full text-sm text-zinc-300 file:mr-4 file:rounded-none file:border-0 file:bg-zinc-800 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-zinc-100 file:hover:bg-zinc-700 cursor-pointer"
                    >
                    <p class="text-xs text-zinc-500 leading-relaxed">
                        Si tienes un CSV con los datos de tus socios, puedes subirlo para realizar un análisis más completo.
                    </p>
                    @error('csv')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <input type="hidden" name="members" value="{{ old('members') }}">
                <input type="hidden" name="average_fee" value="{{ old('average_fee') }}">
                <input type="hidden" name="inactive_members" value="{{ old('inactive_members') }}">
                <input type="hidden" name="monthly_cancellations" value="{{ old('monthly_cancellations') }}">
                <input type="hidden" name="estimated_opportunity" value="{{ old('estimated_opportunity') }}">

                @if ($errors->has('members') || $errors->has('average_fee') || $errors->has('inactive_members') || $errors->has('monthly_cancellations'))
                    <p class="text-sm text-red-400">
                        Usa la calculadora y obtén tu resultado antes de enviar el formulario.
                    </p>
                @endif

                <div class="grid gap-2">
                    <label for="consent" class="flex items-start gap-3 text-sm text-zinc-400 leading-relaxed cursor-pointer">
                        <input
                            id="consent"
                            name="consent"
                            type="checkbox"
                            value="1"
                            required
                            class="mt-1 h-4 w-4 rounded-none border-zinc-800 bg-zinc-950 text-emerald-400 focus:ring-emerald-400"
                        >
                        <span>
                            Acepto que mis datos sean tratados para contactarme con el análisis.
                            Consulta la
                            <a href="{{ route('privacy') }}" class="text-emerald-400 hover:text-emerald-300 underline underline-offset-2">política de privacidad</a>.
                        </span>
                    </label>
                    @error('consent')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full rounded-sm bg-emerald-400 px-8 py-4 text-sm font-bold text-zinc-950 hover:bg-emerald-300 transition-colors"
                    >
                        Ver mi análisis gratuito
                    </button>
                    <p class="mt-3 text-xs text-zinc-500 leading-relaxed">
                        Sin compromiso. No necesitas cambiar tu software.
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>