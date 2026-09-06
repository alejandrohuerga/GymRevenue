@extends('layouts.app')

@section('title', 'Aviso legal — GymRevenue')
@section('meta_description', 'Aviso legal de GymRevenue: información del titular y condiciones de uso del servicio.')

@section('content')
    <x-page-hero eyebrow="Legal" title="Aviso legal" />

    <section class="max-w-3xl mx-auto px-6 py-12 md:py-16">
        <div class="grid gap-10 text-sm text-zinc-400 leading-relaxed">
            <div>
                <h2 class="text-lg font-bold text-zinc-100 mb-3">Titular</h2>
                <p>
                    GymRevenue es un servicio de análisis de ingresos para gimnasios independientes,
                    actualmente en fase de validación. El titular se identificará en la dirección
                    de contacto disponibles durante el alta definitiva del servicio.
                </p>
            </div>

            <div>
                <h2 class="text-lg font-bold text-zinc-100 mb-3">Contacto</h2>
                <p>
                    Puedes contactar con nosotros a través del formulario de contacto disponible
                    en la página de inicio.
                </p>
            </div>

            <div>
                <h2 class="text-lg font-bold text-zinc-100 mb-3">Uso del servicio</h2>
                <p>
                    La calculadora ofrece una estimación orientativa y no constituye una
                    afirmación de ingresos reales perdidos ni garantiza resultados.
                    Los datos mostrados como ejemplo en la web son demostrativos.
                </p>
            </div>

            <p>
                Consulta también la <a href="{{ route('privacy') }}" class="text-emerald-400 hover:text-emerald-300 underline underline-offset-2">política de privacidad</a>
                y la <a href="{{ route('cookies') }}" class="text-emerald-400 hover:text-emerald-300 underline underline-offset-2">política de cookies</a>.
            </p>
        </div>
    </section>
@endsection