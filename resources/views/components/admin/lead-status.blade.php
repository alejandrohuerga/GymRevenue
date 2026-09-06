@props(['status' => 'new'])

@php
    $map = [
        'new' => ['Nuevo', 'border-sky-500/30 bg-sky-500/10 text-sky-400'],
        'contacted' => ['Contactado', 'border-zinc-500/30 bg-zinc-500/10 text-zinc-300'],
        'interested' => ['Interesado', 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400'],
        'proposal' => ['Propuesta', 'border-amber-500/30 bg-amber-500/10 text-amber-400'],
        'won' => ['Ganado', 'border-emerald-400/40 bg-emerald-400/15 text-emerald-300'],
        'lost' => ['Perdido', 'border-red-500/30 bg-red-500/10 text-red-400'],
    ];

    [$label, $classes] = $map[$status] ?? ['Nuevo', 'border-sky-500/30 bg-sky-500/10 text-sky-400'];
@endphp

<span class="inline-flex items-center rounded-sm border px-2 py-0.5 text-xs font-semibold {{ $classes }}">
    {{ $label }}
</span>