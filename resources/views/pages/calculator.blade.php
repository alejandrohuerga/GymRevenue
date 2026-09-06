@extends('layouts.app')

@section('title', 'Calculadora de ingresos perdidos para gimnasios — GymRevenue')
@section('meta_description', 'Estima cuánto podrías recuperar identificando socios inactivos y otras oportunidades. Usa la calculadora de GymRevenue gratis, sin crear cuenta.')

@section('content')
    <x-page-hero
        eyebrow="Calculadora"
        title="Calculadora de ingresos perdidos para gimnasios"
        lead="Estima cuánto podrías recuperar identificando socios inactivos y otras oportunidades. Sin crear cuenta."
    />

    <x-calculator />

    <section id="metodologia" class="max-w-6xl mx-auto px-6 py-16 scroll-mt-20">
        <div class="grid gap-px bg-zinc-800 border border-zinc-800 md:grid-cols-2">
            <div class="bg-zinc-950 p-8">
                <p class="text-sm uppercase tracking-widest text-emerald-400">Metodología</p>
                <p class="mt-4 text-sm text-zinc-400 leading-relaxed">
                    La estimación se calcula con tus datos:
                    <span class="text-zinc-100">(socios inactivos + bajas mensuales) × cuota media</span>.
                    A ese valor mensual se aplica un factor conservador de recuperación del 50%.
                </p>
            </div>
            <div class="bg-zinc-950 p-8">
                <p class="text-sm uppercase tracking-widest text-zinc-500">Nota</p>
                <p class="mt-4 text-sm text-zinc-400 leading-relaxed">
                    Esta cifra es una estimación orientativa basada en los datos introducidos
                    y no garantiza ingresos recuperables. Se ajustará con datos reales.
                </p>
            </div>
        </div>
    </section>

    <x-lead-form />

    <x-faq />

    <section class="max-w-6xl mx-auto px-6 py-8 border-t border-zinc-800">
        <p class="text-xs uppercase tracking-widest text-zinc-500 mb-4">Información legal</p>
        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-zinc-400">
            <a href="{{ route('legal') }}" class="hover:text-zinc-100 transition-colors">Aviso legal</a>
            <a href="{{ route('privacy') }}" class="hover:text-zinc-100 transition-colors">Privacidad</a>
            <a href="{{ route('cookies') }}" class="hover:text-zinc-100 transition-colors">Cookies</a>
        </div>
    </section>
@endsection