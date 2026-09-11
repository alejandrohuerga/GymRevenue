@extends('layouts.app')

@section('title', 'Gracias — GymRevenue')
@section('meta_description', 'Hemos recibido tus datos. Estamos preparando el siguiente paso de tu análisis.')

@section('content')
    <section class="max-w-6xl mx-auto px-6 py-24">
        <div class="max-w-xl">
            <span class="flex h-12 w-12 items-center justify-center border border-emerald-400 text-emerald-400">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </span>

            <h1 class="mt-8 text-4xl sm:text-5xl font-black tracking-tight leading-[1.05] text-balance">
                Tu solicitud ha sido recibida.
            </h1>
            <p class="mt-6 text-zinc-400 leading-relaxed">
                El siguiente paso es analizar los datos de tu gimnasio
                para identificar las oportunidades que podrías estar dejando pasar.
            </p>

            <div class="mt-8 border border-zinc-800 p-6 text-sm text-zinc-400 leading-relaxed">
                <p class="font-semibold text-zinc-100">Qué pasa ahora:</p>
                <ol class="mt-3 grid gap-2 list-decimal list-inside">
                    <li>Revisamos los datos de tu gimnasio.</li>
                    <li>Identificamos las oportunidades prioritarias.</li>
                    <li>Te contactamos con el análisis y los próximos pasos.</li>
                </ol>
            </div>

            <div class="mt-10 flex flex-wrap items-center gap-6">
                <a
                    href="{{ route('how-it-works') }}"
                    class="rounded-sm bg-emerald-400 px-8 py-4 text-sm font-bold text-zinc-950 hover:bg-emerald-300 transition-colors"
                >
                    Ver cómo funciona GymRevenue
                </a>
                <a href="{{ route('calculator') }}" class="text-sm text-zinc-400 hover:text-zinc-100 transition-colors">
                    Volver a la calculadora
                </a>
            </div>
        </div>
    </section>
@endsection