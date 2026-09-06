@extends('layouts.admin')

@section('title', 'Leads — Panel GymRevenue')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-10">
        @if (session('status'))
            <div class="mb-6 rounded-sm border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <p class="text-sm uppercase tracking-widest text-emerald-400 mb-2">Administración</p>
        <h1 class="text-2xl md:text-3xl font-black tracking-tight">Leads</h1>

        <form method="GET" action="{{ route('admin.leads.index') }}" class="mt-6 flex flex-wrap items-center gap-3">
            <label for="status" class="text-sm text-zinc-400">Estado</label>
            <select
                id="status"
                name="status"
                class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 focus:outline-none focus:border-emerald-400"
            >
                <option value="">Todos</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($currentStatus === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button
                type="submit"
                class="rounded-sm border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-200 hover:border-emerald-400 hover:text-emerald-400 transition-colors"
            >
                Filtrar
            </button>
            @if ($currentStatus)
                <a href="{{ route('admin.leads.index') }}" class="text-sm text-zinc-500 hover:text-zinc-300">Limpiar</a>
            @endif
        </form>

        <div class="mt-6 overflow-x-auto border border-zinc-800">
            <table class="w-full text-left text-sm min-w-[900px]">
                <thead class="border-b border-zinc-800 text-xs uppercase tracking-wider text-zinc-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Gimnasio</th>
                        <th class="px-4 py-3 font-semibold">Contacto</th>
                        <th class="px-4 py-3 font-semibold">Email</th>
                        <th class="px-4 py-3 font-semibold text-right">Socios</th>
                        <th class="px-4 py-3 font-semibold text-right">Cuota</th>
                        <th class="px-4 py-3 font-semibold text-right">Inactivos</th>
                        <th class="px-4 py-3 font-semibold text-right">Bajas</th>
                        <th class="px-4 py-3 font-semibold text-right">Oportunidad</th>
                        <th class="px-4 py-3 font-semibold">Fecha</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">CSV</th>
                        <th class="px-4 py-3 font-semibold">Análisis</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/70">
                    @forelse ($leads as $lead)
                        <tr class="hover:bg-zinc-900/60 transition-colors">
                            <td class="px-4 py-3 align-top">
                                <a
                                    href="{{ route('admin.leads.show', $lead) }}"
                                    class="font-semibold text-emerald-400 hover:text-emerald-300"
                                >
                                    {{ $lead->gym_name }}
                                </a>
                                @if ($lead->software)
                                    <span class="block text-xs text-zinc-500">{{ $lead->software }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">{{ $lead->contact_name }}</td>
                            <td class="px-4 py-3 align-top text-zinc-400">{{ $lead->email }}</td>
                            <td class="px-4 py-3 align-top text-right tabular-nums">{{ number_format($lead->members, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 align-top text-right tabular-nums">{{ number_format($lead->average_fee, 2, ',', '.') }} €</td>
                            <td class="px-4 py-3 align-top text-right tabular-nums">{{ number_format($lead->inactive_members, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 align-top text-right tabular-nums">{{ number_format($lead->monthly_cancellations, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 align-top text-right font-semibold tabular-nums text-emerald-400">
                                {{ number_format($lead->estimated_opportunity, 0, ',', '.') }} €/mes
                            </td>
                            <td class="px-4 py-3 align-top text-zinc-400">{{ $lead->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 align-top">
                                <x-admin.lead-status :status="$lead->status" />
                            </td>
<td class="px-4 py-3 align-top">
@if ($lead->csv_path)
    <span class="font-semibold text-emerald-400">Sí</span>
@else
    <span class="text-zinc-600">No</span>
@endif
</td>
<td class="px-4 py-3 align-top">
@if ($lead->analysis !== null && $lead->analysis->members_valid > 0)
    <a
        href="{{ route('admin.leads.report', $lead) }}"
        class="inline-flex items-center rounded-sm border border-emerald-500/30 px-2 py-0.5 text-xs font-semibold text-emerald-400 hover:border-emerald-400 transition-colors"
    >
        Disponible
    </a>
@elseif ($lead->csv_path && $lead->analysis !== null)
    <span class="inline-flex items-center rounded-sm border border-amber-500/30 px-2 py-0.5 text-xs font-semibold text-amber-400">
        Sin datos válidos
    </span>
@elseif ($lead->csv_path)
    <span class="text-xs text-zinc-600">Pendiente</span>
@else
    <span class="text-xs text-zinc-700">—</span>
@endif
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-12 text-center text-zinc-500">
                                Aún no hay leads que mostrar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $leads->links() }}
        </div>
    </div>
@endsection