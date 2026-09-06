@props([
    'report',
    'context' => 'public',
    'publicUrl' => null,
    'contactUrl' => null,
    'lead' => null,
])

@php
    $fmt = fn (float $value): string => str_ends_with(number_format($value, 2, ',', '.'), ',00')
        ? substr(number_format($value, 2, ',', '.'), 0, -3)
        : number_format($value, 2, ',', '.');
    $reg = fn (int $n): string => $n === 1 ? 'registro' : 'registros';
    $isAdmin = $context === 'admin';
@endphp

<header class="mt-8 border-b border-zinc-800 pb-8">
    <p class="text-sm uppercase tracking-widest text-emerald-400">Informe comercial</p>
    <h1 class="mt-2 text-3xl md:text-4xl font-black tracking-tight">Análisis de tu gimnasio</h1>
    <p class="mt-2 text-sm text-zinc-500">
        Analizado a fecha {{ $report->referenceDate }}
        @if ($report->usable)
            · {{ $report->validRecords }} {{ $reg($report->validRecords) }} válidos
        @endif
    </p>
</header>

@if (! $report->usable)
    <section class="mt-8 border border-amber-500/20 bg-amber-500/5 p-8 md:p-10">
        <p class="text-sm uppercase tracking-widest text-amber-400">Informe no disponible</p>
        <h2 class="mt-2 text-2xl md:text-3xl font-black tracking-tight">No hemos podido generar el informe</h2>
        <p class="mt-4 text-sm text-zinc-400 leading-relaxed">
            El análisis no contiene registros utilizables. Mostrar cifras basadas en esos datos
            induciría a error, por eso mostramos este aviso en lugar de un informe.
        </p>

        @if ($report->recordsWithErrors > 0)
            <p class="mt-4 text-sm text-zinc-400">
                Se analizaron {{ $report->totalRecords }} {{ $reg($report->totalRecords) }} y
                {{ $report->recordsWithErrors === 1 ? '1 presentaba errores' : $report->recordsWithErrors.' presentaban errores' }}.
            </p>

            @if ($report->qualityErrors !== [])
                <div class="mt-5">
                    <p class="text-xs uppercase tracking-widest text-zinc-500">Calidad de los datos</p>
                    <ul class="mt-3 grid gap-1 text-sm text-zinc-400">
                        @foreach ($report->qualityErrors as $error)
                            <li class="flex gap-2">
                                <span class="text-amber-400">{{ $error['count'] }}×</span>
                                <span>{{ $error['label'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

        @if ($isAdmin)
            <div class="mt-8">
                <a
                    href="{{ route('admin.leads.show', $lead) }}"
                    class="inline-block rounded-sm border border-zinc-700 px-6 py-3 text-sm font-semibold text-zinc-200 hover:border-emerald-400 hover:text-emerald-400 transition-colors"
                >
                    Volver al lead
                </a>
            </div>
        @endif
    </section>
@else
    @php
        $heroAmount = $report->reactivationPotential > 0 ? $report->reactivationPotential : $report->monthlyValueAtRisk;
        $heroLabel = $report->reactivationPotential > 0
            ? 'Potencial de reactivación'
            : 'Cuotas de socios con baja actividad';
        $heroSuffix = $report->reactivationPotential > 0 ? ' €' : ' €/mes';
        $heroCaption = $report->reactivationPotential > 0
            ? 'Estimación de facturación potencial durante 3 meses si se consiguiera reactivar el grupo detectado.'
            : 'Cuotas mensuales asociadas a socios activos que llevan más de 60 días sin registrar una visita.';
    @endphp

    @if ($report->reactivationPotential > 0 || $report->monthlyValueAtRisk > 0)
        <section class="mt-8 border border-emerald-500/20 bg-emerald-500/5 p-8 md:p-12 text-center" aria-labelledby="oportunidad-economica">
            <h2 id="oportunidad-economica" class="text-sm uppercase tracking-widest text-emerald-400">{{ $heroLabel }}</h2>
            <p class="mt-4 text-5xl md:text-6xl font-black tracking-tight text-zinc-50 tabular-nums">
                {{ $fmt($heroAmount) }}<span class="text-2xl md:text-4xl text-emerald-400">{{ $heroSuffix }}</span>
            </p>
            <p class="mx-auto mt-4 max-w-md text-sm text-zinc-400 leading-relaxed">{{ $heroCaption }}</p>
            <p class="mt-5 text-xs text-zinc-600">
                Cifra estimada y orientativa. No representa dinero perdido de forma exacta ni garantiza ingresos recuperables.
            </p>
        </section>
    @endif

    <section class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4" aria-label="Resumen">
        @if ($report->activeMembers > 0)
            <div class="border border-zinc-800 p-5">
                <p class="text-xs uppercase tracking-widest text-zinc-500">Socios activos</p>
                <p class="mt-2 text-3xl font-black tabular-nums">{{ $report->activeMembers }}</p>
            </div>
        @endif

        @if ($report->inactiveMembers > 0)
            <div class="border border-zinc-800 p-5">
                <p class="text-xs uppercase tracking-widest text-zinc-500">Socios inactivos</p>
                <p class="mt-2 text-3xl font-black tabular-nums">{{ $report->inactiveMembers }}</p>
            </div>
        @endif

        @if ($report->recentCancellations > 0)
            <div class="border border-zinc-800 p-5">
                <p class="text-xs uppercase tracking-widest text-zinc-500">Bajas recientes</p>
                <p class="mt-2 text-3xl font-black tabular-nums">{{ $report->recentCancellations }}</p>
                <p class="mt-1 text-xs text-zinc-600">últimos 90 días</p>
            </div>
        @endif

        @if ($report->activeLowActivity > 0)
            <div class="border border-zinc-800 p-5">
                <p class="text-xs uppercase tracking-widest text-zinc-500">Baja actividad</p>
                <p class="mt-2 text-3xl font-black tabular-nums">{{ $report->activeLowActivity }}</p>
                <p class="mt-1 text-xs text-zinc-600">+60 días sin visitar</p>
            </div>
        @endif
    </section>

    <section class="mt-12" aria-labelledby="oportunidades">
        <h2 id="oportunidades" class="text-2xl md:text-3xl font-black tracking-tight">¿Qué hemos encontrado?</h2>

        @forelse ($report->opportunities as $opportunity)
            <div class="mt-4 border border-zinc-800 p-6 md:p-8">
                <h3 class="font-bold text-zinc-100">{{ $opportunity['title'] }}</h3>
                <p class="mt-3 text-sm text-zinc-400 leading-relaxed">{{ $opportunity['text'] }}</p>
            </div>
        @empty
            <p class="mt-4 text-sm text-zinc-400">No hemos detectado oportunidades que requieran tu atención en este momento.</p>
        @endforelse

        @php
            $neutralNotes = [];
            if ($report->inactiveMembers === 0) {
                $neutralNotes[] = 'No hemos detectado socios inactivos en el archivo.';
            }
            if ($report->activeLowActivity === 0) {
                $neutralNotes[] = 'No hemos detectado socios activos con más de 60 días sin registrar una visita.';
            }
            if ($report->recentCancellations === 0) {
                $neutralNotes[] = 'No hemos detectado bajas durante los últimos 90 días.';
            }
        @endphp

        @if ($neutralNotes !== [])
            <div class="mt-6 grid gap-2">
                @foreach ($neutralNotes as $note)
                    <p class="flex items-start gap-3 text-sm text-zinc-500">
                        <span class="mt-1.5 inline-block h-1.5 w-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                        <span>{{ $note }}</span>
                    </p>
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-12" aria-labelledby="calidad">
        <h2 id="calidad" class="text-xs uppercase tracking-widest text-zinc-500">Calidad de los datos</h2>

        @if ($report->recordsWithErrors > 0)
            <p class="mt-3 text-sm text-zinc-400 leading-relaxed">
                Hemos analizado {{ $report->totalRecords }} {{ $reg($report->totalRecords) }}:
                {{ $report->validRecords }} válidos y
                {{ $report->recordsWithErrors === 1 ? '1 con errores' : $report->recordsWithErrors.' con errores' }}.
                Los registros con errores no se han utilizado para el cálculo de las cifras.
            </p>

            @if ($report->qualityErrors !== [])
                <ul class="mt-4 grid gap-1.5 text-sm text-zinc-500">
                    @foreach ($report->qualityErrors as $error)
                        <li class="flex gap-2">
                            <span class="text-zinc-600">{{ $error['count'] }}×</span>
                            <span>{{ $error['label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <p class="mt-4 text-xs text-zinc-600">Revisa las fechas, estados y cuotas de estos registros para que puedan incluirse en el próximo análisis.</p>
        @else
            <p class="mt-3 text-sm text-zinc-500">
                Se analizaron {{ $report->totalRecords }} {{ $reg($report->totalRecords) }} y todos eran válidos.
            </p>
        @endif
    </section>

    <section class="mt-12 border border-emerald-500/20 bg-emerald-500/5 p-8 md:p-10 text-center" aria-labelledby="cta">
        <h2 id="cta" class="text-2xl md:text-3xl font-black tracking-tight">¿Quieres saber cómo aprovechar estas oportunidades?</h2>
        <p class="mx-auto mt-3 max-w-md text-sm text-zinc-400 leading-relaxed">
            Podemos ayudarte a convertir estos datos en acciones concretas para recuperar socios y mejorar la retención.
        </p>

        @if ($isAdmin)
            @if ($publicUrl)
                <div class="mt-8">
                    <a
                        href="{{ $publicUrl }}"
                        target="_blank"
                        rel="noopener"
                        class="inline-block rounded-sm bg-emerald-400 px-8 py-4 text-sm font-bold text-zinc-950 hover:bg-emerald-300 transition-colors"
                    >
                        Ver informe público
                    </a>
                    <p class="mt-3 text-xs text-zinc-600">Abre el informe en una página pública y segura que puedes compartir con el gimnasio.</p>
                </div>
            @else
                <p class="mt-6 text-sm text-zinc-500">El análisis aún no dispone de un enlace público. Vuelve a guardar el análisis.</p>
            @endif
        @else
            @if (session('contact_sent'))
                <div class="mt-6 rounded-sm border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300" role="status">
                    {{ session('contact_sent') }}
                </div>
            @else
                <form
                    method="POST"
                    action="{{ $contactUrl }}"
                    class="mx-auto mt-8 grid max-w-md gap-4 text-left"
                >
                    @csrf

                    <div class="grid gap-2">
                        <label for="contact-name" class="text-sm text-zinc-400">Tu nombre</label>
                        <input
                            id="contact-name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            required
                            maxlength="255"
                            autocomplete="name"
                            class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                            placeholder="Nombre y apellidos"
                        >
                        @error('name')
                            <p class="text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-2">
                        <label for="contact-email" class="text-sm text-zinc-400">Email de contacto</label>
                        <input
                            id="contact-email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            maxlength="255"
                            autocomplete="email"
                            class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                            placeholder="tucorreo@ejemplo.com"
                        >
                        @error('email')
                            <p class="text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-2">
                        <label for="contact-message" class="text-sm text-zinc-400">Mensaje (opcional)</label>
                        <textarea
                            id="contact-message"
                            name="message"
                            rows="3"
                            maxlength="2000"
                            class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                            placeholder="Cuéntanos qué quieres conseguir."
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <input type="text" name="website" value="" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">

                    @error('consent')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror

                    <div>
                        <button
                            type="submit"
                            class="rounded-sm bg-emerald-400 px-8 py-4 text-sm font-bold text-zinc-950 hover:bg-emerald-300 transition-colors"
                        >
                            Quiero mejorar mi gimnasio
                        </button>
                    </div>

                    <p class="text-xs text-zinc-600 leading-relaxed">
                        Al enviar este formulario aceptas el tratamiento de tus datos para gestionar tu solicitud.
                        Consulta la <a href="{{ route('privacy') }}" class="underline hover:text-zinc-400">política de privacidad</a>.
                    </p>
                </form>
            @endif
        @endif
    </section>
@endif