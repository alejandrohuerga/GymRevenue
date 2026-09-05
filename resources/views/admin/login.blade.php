@extends('layouts.admin')

@section('title', 'Acceso — Panel GymRevenue')

@section('content')
    <div class="max-w-md mx-auto px-6 py-16 md:py-24">
        <div class="border border-zinc-800 p-8">
            <p class="text-sm uppercase tracking-widest text-emerald-400 mb-2">Acceso restringido</p>
            <h1 class="text-2xl font-black tracking-tight">Panel de administración</h1>
            <p class="mt-2 text-sm text-zinc-400 leading-relaxed">Introduce tus credenciales para gestionar los leads.</p>

            <form method="POST" action="{{ route('admin.login.store') }}" class="mt-8 grid gap-5">
                @csrf

                <div class="grid gap-2">
                    <label for="email" class="text-sm text-zinc-400">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        autofocus
                        class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                        placeholder="admin@gymrevenue.com"
                    >
                    @error('email')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-2">
                    <label for="password" class="text-sm text-zinc-400">Contraseña</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="rounded-none border border-zinc-800 bg-zinc-950 px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400"
                    >
                    @error('password')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button
                        type="submit"
                        class="rounded-sm bg-emerald-400 px-8 py-3 text-sm font-bold text-zinc-950 hover:bg-emerald-300 transition-colors"
                    >
                        Entrar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection