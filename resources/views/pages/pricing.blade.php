@extends('layouts.app')

@section('title', 'Precios — GymRevenue')
@section('meta_description', 'Planes sencillos para que los gimnasios independientes aprovechen sus datos: recupera clientes inactivos y reduce bajas.')

@section('content')
    <x-page-hero
        eyebrow="Precios"
        title="Sencillos. Sin sorpresas."
        lead="Precios provisionales durante la fase de validación. El análisis inicial es gratuito."
    />

    <section class="max-w-6xl mx-auto px-6 py-16 md:py-24">
        <div class="grid md:grid-cols-3 gap-px bg-zinc-800 border border-zinc-800">
            <div class="bg-zinc-950 p-8">
                <p class="text-xs uppercase tracking-widest text-zinc-500">Starter</p>
                <p class="mt-4 text-5xl font-black">49 €<span class="text-sm font-normal text-zinc-500">/mes</span></p>
                <ul class="mt-6 grid gap-3 text-sm text-zinc-400">
                    <li class="flex items-start gap-2"><span class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400">✓</span>Análisis básico de fuga</li>
                    <li class="flex items-start gap-2"><span class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400">✓</span>Lista priorizada de oportunidades</li>
                    <li class="flex items-start gap-2"><span class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400">✓</span>1 centro</li>
                </ul>
            </div>

            <div class="bg-zinc-950 p-8 border-x-0 md:border-x border-t md:border-t-0 border-zinc-800">
                <p class="text-xs uppercase tracking-widest text-emerald-400">Growth</p>
                <p class="mt-4 text-5xl font-black">99 €<span class="text-sm font-normal text-zinc-500">/mes</span></p>
                <ul class="mt-6 grid gap-3 text-sm text-zinc-100">
                    <li class="flex items-start gap-2"><span class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400">✓</span>Todo lo de Starter</li>
                    <li class="flex items-start gap-2"><span class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400">✓</span>Seguimiento de leads</li>
                    <li class="flex items-start gap-2"><span class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400">✓</span>Hasta 3 centros</li>
                </ul>
            </div>

            <div class="bg-zinc-950 p-8">
                <p class="text-xs uppercase tracking-widest text-zinc-500">Pro</p>
                <p class="mt-4 text-5xl font-black">199 €<span class="text-sm font-normal text-zinc-500">/mes</span></p>
                <ul class="mt-6 grid gap-3 text-sm text-zinc-400">
                    <li class="flex items-start gap-2"><span class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400">✓</span>Todo lo de Growth</li>
                    <li class="flex items-start gap-2"><span class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400">✓</span>Centros ilimitados</li>
                    <li class="flex items-start gap-2"><span class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400">✓</span>Mesa de resultados</li>
                </ul>
            </div>
        </div>

        <p class="mt-6 text-xs text-zinc-500">
            Precios provisionales hasta validar disposición a pagar.
        </p>
    </section>

    <x-final-cta />
@endsection