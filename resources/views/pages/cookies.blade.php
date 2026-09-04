@extends('layouts.app')

@section('title', 'Política de cookies — GymRevenue')
@section('meta_description', 'Política de cookies de GymRevenue: qué cookies utilizamos y cómo se gestionan.')

@section('content')
    <x-page-hero eyebrow="Legal" title="Política de cookies" />

    <section class="max-w-3xl mx-auto px-6 py-12 md:py-16">
        <div class="grid gap-10 text-sm text-zinc-400 leading-relaxed">
            <div>
                <h2 class="text-lg font-bold text-zinc-100 mb-3">Qué cookies usamos</h2>
                <p>
                    GymRevenue utiliza únicamente las cookies técnicas necesarias para el
                    funcionamiento del sitio, como el token CSRF que protege los formularios.
                    No utilizamos cookies de publicidad ni de seguimiento de terceros.
                </p>
            </div>

            <div>
                <h2 class="text-lg font-bold text-zinc-100 mb-3">Cómo gestionarlas</h2>
                <p>
                    Puedes configurar tu navegador para bloquear o eliminar las cookies.
                    Ten en cuenta que algunas funciones del sitio podrían dejar de funcionar.
                </p>
            </div>
        </div>
    </section>
@endsection