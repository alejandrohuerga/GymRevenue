@extends('layouts.admin')

@section('title', $lead->gym_name.' — Panel GymRevenue')

@section('content')
    <div class="max-w-4xl mx-auto px-6 py-10">
        <a href="{{ route('admin.leads.index') }}" class="text-sm text-zinc-400 hover:text-zinc-100 transition-colors">← Volver a leads</a>

        @if (session('status'))
            <div class="mt-6 rounded-sm border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-widest text-emerald-400 mb-2">Lead</p>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight">{{ $lead->gym_name }}</h1>
                <p class="mt-1 text-sm text-zinc-400">{{ $lead->contact_name }} · {{ $lead->email }}</p>
            </div>
            <x-admin.lead-status :status="$lead->status" />
        </div>

        <section class="mt-8 border border-zinc-800 p-6 md:p-8" aria-labelledby="lead-data">
            <h2 id="lead-data" class="text-sm uppercase tracking-widest text-zinc-500">Datos del gimnasio</h2>
            <dl class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-6 text-sm">
                <div>
                    <dt class="text-zinc-500">Socios</dt>
                    <dd class="mt-1 font-semibold tabular-nums">{{ number_format($lead->members, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Cuota media</dt>
                    <dd class="mt-1 font-semibold tabular-nums">{{ number_format($lead->average_fee, 2, ',', '.') }} €</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Inactivos</dt>
                    <dd class="mt-1 font-semibold tabular-nums">{{ number_format($lead->inactive_members, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Bajas/mes</dt>
                    <dd class="mt-1 font-semibold tabular-nums">{{ number_format($lead->monthly_cancellations, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Software</dt>
                    <dd class="mt-1 font-semibold">{{ $lead->software ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Oportunidad mensual</dt>
                    <dd class="mt-1 font-semibold text-emerald-400 tabular-nums">{{ number_format($lead->estimated_opportunity, 2, ',', '.') }} €/mes</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Oportunidad anual (ref.)</dt>
                    <dd class="mt-1 font-semibold text-emerald-400 tabular-nums">{{ number_format($lead->estimated_opportunity * 12, 2, ',', '.') }} €/año</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Recibido el</dt>
                    <dd class="mt-1 font-semibold tabular-nums">{{ $lead->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>

            <p class="mt-6 text-xs text-zinc-500 leading-relaxed border-t border-zinc-800 pt-4">
                Cifra estimada según los datos introducidos por el lead. No representa dinero perdido de forma exacta ni garantiza ingresos recuperables.
            </p>
        </section>

        <section class="mt-6 border border-zinc-800 p-6 md:p-8" aria-labelledby="lead-csv">
            <h2 id="lead-csv" class="text-sm uppercase tracking-widest text-zinc-500">CSV de socios</h2>
            @if ($csv !== null && $csv['exists'])
                <div class="mt-4 flex items-center gap-2 text-sm">
                    <span class="font-semibold text-emerald-400">Sí</span>
                    <a
                        href="{{ route('admin.leads.csv', $lead) }}"
                        class="rounded-sm border border-zinc-700 px-3 py-1.5 text-xs font-semibold text-zinc-200 hover:border-emerald-400 hover:text-emerald-400 transition-colors"
                    >
                        Descargar CSV
                    </a>
                </div>
                <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-zinc-500">Nombre original</dt>
                        <dd class="mt-1 font-semibold break-all">{{ $csv['original_name'] ?: 'socios.csv' }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Subido el</dt>
                        <dd class="mt-1 font-semibold">{{ $csv['uploaded_at']?->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Tamaño</dt>
                        <dd class="mt-1 font-semibold">
                            @if ($csv['size'] !== null)
                                {{ number_format($csv['size'], 0, ',', '.') }} bytes
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </dl>
            @elseif ($csv === null)
                <p class="mt-4 text-sm text-zinc-400">No</p>
            @else
                <div class="mt-4 flex items-center gap-2 text-sm">
                    <span class="font-semibold text-emerald-400">Sí</span>
                    <span class="text-xs text-zinc-500">(el archivo ya no está disponible)</span>
                </div>
            @endif
        </section>

        <section class="mt-6 border border-zinc-800 p-6 md:p-8" aria-labelledby="lead-analysis">
            <h2 id="lead-analysis" class="text-sm uppercase tracking-widest text-zinc-500">Análisis del CSV</h2>

            @if ($analysis === null)
                <p class="mt-4 text-sm text-zinc-400">Análisis pendiente</p>
                @if ($csv !== null)
                    <form method="POST" action="{{ route('admin.leads.analyze', $lead) }}" class="mt-4">
                        @csrf
                        <button
                            type="submit"
                            class="rounded-sm border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-200 hover:border-emerald-400 hover:text-emerald-400 transition-colors"
                        >
                            Analizar CSV
                        </button>
                    </form>
                @endif
            @else
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <p class="text-sm text-zinc-500">Analizado a fecha {{ $analysis->reference_date->format('d/m/Y') }}</p>
                    @if ($analysis->members_valid > 0)
                        <a
                            href="{{ route('admin.leads.report', $lead) }}"
                            class="rounded-sm border border-emerald-500/30 px-3 py-1.5 text-xs font-semibold text-emerald-400 hover:border-emerald-400 transition-colors"
                        >
                            Ver informe comercial
                        </a>
                    @endif
                </div>

                <dl class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-6 text-sm">
                    <div>
                        <dt class="text-zinc-500">Registros totales</dt>
                        <dd class="mt-1 font-semibold tabular-nums">{{ number_format($analysis->members_total, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Registros válidos</dt>
                        <dd class="mt-1 font-semibold tabular-nums">{{ number_format($analysis->members_valid, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Con errores</dt>
                        <dd class="mt-1 font-semibold tabular-nums">{{ number_format($analysis->members_with_errors, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Active</dt>
                        <dd class="mt-1 font-semibold tabular-nums">{{ number_format($analysis->status_active, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Inactive</dt>
                        <dd class="mt-1 font-semibold tabular-nums">{{ number_format($analysis->status_inactive, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Cancelled</dt>
                        <dd class="mt-1 font-semibold tabular-nums">{{ number_format($analysis->status_cancelled, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Active con &gt;60 días sin visitar</dt>
                        <dd class="mt-1 font-semibold text-amber-400 tabular-nums">{{ number_format($analysis->active_at_risk + $analysis->active_high_risk, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Active con &gt;90 días sin visitar</dt>
                        <dd class="mt-1 font-semibold text-red-400 tabular-nums">{{ number_format($analysis->active_high_risk, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Bajas últimos 90 días</dt>
                        <dd class="mt-1 font-semibold tabular-nums">{{ number_format($analysis->cancellations_last_90_days, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Cuota media</dt>
                        <dd class="mt-1 font-semibold tabular-nums">
                            {{ $analysis->fees_average !== null ? number_format($analysis->fees_average, 2, ',', '.').' €' : '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Cuotas de socios con baja actividad</dt>
                        <dd class="mt-1 font-semibold text-emerald-400 tabular-nums">{{ number_format($analysis->value_at_risk, 2, ',', '.') }} €/mes</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500">Potencial de reactivación (3 meses)</dt>
                        <dd class="mt-1 font-semibold text-emerald-400 tabular-nums">{{ number_format($analysis->reactivation_potential, 2, ',', '.') }} €</dd>
                    </div>
                </dl>

                @if ($analysis->quality !== [])
                    <div class="mt-6">
                        <h3 class="text-xs uppercase tracking-widest text-zinc-500">Calidad del CSV</h3>
                        <ul class="mt-3 space-y-1 text-sm text-zinc-400">
                            @foreach (collect($analysis->quality)->groupBy('type') as $type => $items)
                                <li>{{ count($items) }} × {{ \App\Services\Analysis\CsvAnalysisEngine::QUALITY_LABELS[$type] ?? $type }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($analysis->opportunities !== [])
                    <div class="mt-6">
                        <h3 class="text-xs uppercase tracking-widest text-zinc-500">Oportunidades detectadas</h3>
                        <ul class="mt-3 space-y-2 text-sm text-zinc-300">
                            @foreach ($analysis->opportunities as $opportunity)
                                <li class="flex gap-2"><span class="text-emerald-400">·</span><span>{{ $opportunity }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (($analysis->extra['disclaimers'] ?? []) !== [])
                    <p class="mt-6 text-xs text-zinc-500 leading-relaxed border-t border-zinc-800 pt-4">
                        @foreach ($analysis->extra['disclaimers'] as $disclaimer)
                            {{ $disclaimer }}<br>
                        @endforeach
                    </p>
                @endif

                <form method="POST" action="{{ route('admin.leads.analyze', $lead) }}" class="mt-4">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-sm border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-200 hover:border-emerald-400 hover:text-emerald-400 transition-colors"
                    >
                        Re-analizar CSV
                    </button>
                </form>
            @endif
        </section>

        <section class="mt-6 border border-zinc-800 p-6 md:p-8" aria-labelledby="lead-status">
            <h2 id="lead-status" class="text-sm uppercase tracking-widest text-zinc-500">Estado comercial</h2>
            <form method="POST" action="{{ route('admin.leads.status', $lead) }}" class="mt-4 flex flex-wrap items-center gap-3">
                @csrf
                @method('PATCH')

                <label for="status" class="sr-only">Estado del lead</label>
                <select
                    id="status"
                    name="status"
                    class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 focus:outline-none focus:border-emerald-400"
                >
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($lead->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button
                    type="submit"
                    class="rounded-sm border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-200 hover:border-emerald-400 hover:text-emerald-400 transition-colors"
                >
                    Guardar estado
                </button>

                @error('status')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
            </form>
        </section>

        <section class="mt-6 border border-zinc-800 p-6 md:p-8" aria-labelledby="lead-notes">
            <h2 id="lead-notes" class="text-sm uppercase tracking-widest text-zinc-500">Notas internas</h2>
            <form method="POST" action="{{ route('admin.leads.notes', $lead) }}" class="mt-4 grid gap-4">
                @csrf
                @method('PATCH')

                <div class="grid gap-2">
                    <label for="notes" class="sr-only">Notas internas</label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="5"
                        maxlength="5000"
                        class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                        placeholder="Registra aquí la evolución comercial del lead: llamadas, respuestas, acuerdos…"
                    >{{ old('notes', $lead->notes) }}</textarea>
                    @error('notes')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button
                        type="submit"
                        class="rounded-sm border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-200 hover:border-emerald-400 hover:text-emerald-400 transition-colors"
                    >
                        Guardar notas
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection