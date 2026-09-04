@extends('layouts.app')

@section('title', 'Política de privacidad — GymRevenue')
@section('meta_description', 'Política de privacidad de GymRevenue: qué datos recogemos, para qué y cuáles son tus derechos.')

@section('content')
    <x-page-hero eyebrow="Legal" title="Política de privacidad" />

    <section class="max-w-3xl mx-auto px-6 py-12 md:py-16">
        <div class="grid gap-10 text-sm text-zinc-400 leading-relaxed">
            <div>
                <h2 class="text-lg font-bold text-zinc-100 mb-3">Qué datos recogemos</h2>
                <p>
                    Únicamente recogemos los datos que nos proporcionas voluntariamente a través
                    del formulario de contacto: nombre, email, nombre del gimnasio y, si lo indicas,
                    el software que utilizas y algunos datos básicos de tu gimnasio para el cálculo
                    de la estimación.
                </p>
            </div>

            <div>
                <h2 class="text-lg font-bold text-zinc-100 mb-3">Para qué los usamos</h2>
                <p>
                    Utilizamos esos datos para preparar el análisis de tu gimnasio y contactarte
                    con el resultado. No compartimos tus datos con terceros.
                </p>
            </div>

            <div>
                <h2 class="text-lg font-bold text-zinc-100 mb-3">Base de consentimiento</h2>
                <p>
                    El tratamiento se basa en el consentimiento que prestas al marcar la casilla
                    correspondiente en el formulario.
                </p>
            </div>

            <div>
                <h2 class="text-lg font-bold text-zinc-100 mb-3">Tus derechos</h2>
                <p>
                    Puedes solicitar el acceso, rectificación o supresión de tus datos poniéndote
                    en contacto con nosotros a través del formulario de la página de inicio.
                </p>
            </div>
        </div>
    </section>
@endsection