@extends('hub.layout')

@section('title', 'Login')

@section('content')
<div class="flex-1 flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-4xl shadow-2xl mb-6 shadow-indigo-500/30">
                J2
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Bienvenido al Hub</h1>
            <p class="text-gray-400">Ingresa tus credenciales de super administrador.</p>
        </div>

        <form method="POST" action="/hub/login" class="glass-panel p-8 rounded-3xl shadow-xl">
            @csrf
            
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-300 mb-2" for="email">Correo Electrónico</label>
                <input type="email" name="email" id="email" required class="w-full bg-gray-900/50 border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-500" placeholder="admin@ejemplo.com">
            </div>

            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-300 mb-2" for="password">Contraseña</label>
                <input type="password" name="password" id="password" required class="w-full bg-gray-900/50 border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-500" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-indigo-500/25 flex justify-center items-center gap-2">
                Entrar al Hub
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </button>
        </form>
    </div>
</div>
@endsection
