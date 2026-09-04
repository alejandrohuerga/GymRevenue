@extends('layouts.app')

@section('title', 'Cómo funciona GymRevenue')
@section('meta_description', 'Descubre cómo GymRevenue analiza los datos de tu gimnasio, detecta oportunidades de ingresos y te dice dónde centrar tus esfuerzos.')

@section('content')
    <x-page-hero
        eyebrow="Cómo funciona"
        title="Del dato a la acción, paso a paso."
        lead="GymRevenue complementa el software que ya utilizas para detectar oportunidades de ingresos y ayudarte a actuar sobre ellas."
    />

    <x-problem />
    <x-how-it-works />

    <section class="max-w-6xl mx-auto px-6 py-16 md:py-24 grid md:grid-cols-12 gap-12 items-start">
        <div class="md:col-span-5">
            <p class="text-sm uppercase tracking-widest text-emerald-400 mb-4">El proceso</p>
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight text-balance">
                De principio a fin.
            </h2>
            <p class="mt-4 text-zinc-400 leading-relaxed">
                Desde que proporcionas tus datos hasta el momento en que actúas y mides el resultado.
            </p>
        </div>

        <div class="md:col-span-7">
            <ol class="grid gap-px bg-zinc-800 border border-zinc-800">
                <li class="bg-zinc-950 p-6 flex items-baseline gap-6">
                    <span class="text-4xl font-black text-emerald-400">1</span>
                    <div>
                        <h3 class="font-bold">Proporcionas datos</h3>
                        <p class="mt-1 text-sm text-zinc-400">Introduces la información de tu gimnasio.</p>
                    </div>
                </li>
                <li class="bg-zinc-950 p-6 flex items-baseline gap-6">
                    <span class="text-4xl font-black text-emerald-400">2</span>
                    <div>
                        <h3 class="font-bold">Analizamos</h3>
                        <p class="mt-1 text-sm text-zinc-400">Buscamos señales de fuga y oportunidades.</p>
                    </div>
                </li>
                <li class="bg-zinc-950 p-6 flex items-baseline gap-6">
                    <span class="text-4xl font-black text-emerald-400">3</span>
                    <div>
                        <h3 class="font-bold">Detectamos oportunidades</h3>
                        <p class="mt-1 text-sm text-zinc-400">Leads fríos, socios inactivos y en riesgo.</p>
                    </div>
                </li>
                <li class="bg-zinc-950 p-6 flex items-baseline gap-6">
                    <span class="text-4xl font-black text-emerald-400">4</span>
                    <div>
                        <h3 class="font-bold">Priorizamos</h3>
                        <p class="mt-1 text-sm text-zinc-400">Sabes dónde está el mayor valor.</p>
                    </div>
                </li>
                <li class="bg-zinc-950 p-6 flex items-baseline gap-6">
                    <span class="text-4xl font-black text-emerald-400">5</span>
                    <div>
                        <h3 class="font-bold">Actúas</h3>
                        <p class="mt-1 text-sm text-zinc-400">Trabajas sobre la lista de acciones.</p>
                    </div>
                </li>
                <li class="bg-zinc-950 p-6 flex items-baseline gap-6">
                    <span class="text-4xl font-black text-emerald-400">6</span>
                    <div>
                        <h3 class="font-bold">Medimos</h3>
                        <p class="mt-1 text-sm text-zinc-400">Compruebas qué acciones recuperan ingresos.</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <section class="border-y border-zinc-800">
        <div class="max-w-6xl mx-auto px-6 py-16 md:py-24">
            <div class="max-w-2xl">
                <p class="text-sm uppercase tracking-widest text-emerald-400 mb-4">Ejemplo de análisis</p>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight text-balance">
                    Un ejemplo con datos.
                </h2>
            </div>

            <div class="mt-10 grid md:grid-cols-3 gap-px bg-zinc-800 border border-zinc-800">
                <div class="bg-zinc-950 p-8">
                    <p class="text-xs uppercase tracking-widest text-zinc-500">Entrada</p>
                    <p class="mt-3 text-2xl font-black">31 inactivos</p>
                    <p class="mt-1 text-sm text-zinc-400">Socios sin acudir</p>
                </div>
                <div class="bg-zinc-950 p-8">
                    <p class="text-xs uppercase tracking-widest text-zinc-500">Entrada</p>
                    <p class="mt-3 text-2xl font-black">15 bajas/mes</p>
                    <p class="mt-1 text-sm text-zinc-400">Con cuota media de 40 €</p>
                </div>
                <div class="bg-zinc-950 p-8">
                    <p class="text-xs uppercase tracking-widest text-emerald-400">Resultado</p>
                    <p class="mt-3 text-3xl font-black text-emerald-400">920 €/mes</p>
                    <p class="mt-1 text-sm text-zinc-400">Oportunidad estimada</p>
                </div>
            </div>

            <p class="mt-6 text-sm text-zinc-500">
                Datos demostrativos para ilustrar la metodología.
            </p>
        </div>
    </section>

    <x-dashboard-preview />
    <x-benefits />
    <x-final-cta />
@endsection