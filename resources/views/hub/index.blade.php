@extends('hub.layout')

@section('title', 'Dashboard')

@section('content')
<div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Visión General</h1>
        <p class="text-gray-400 text-sm md:text-base">Estado de la API y aplicaciones conectadas al J2 Hub.</p>
    </div>
    <form method="POST" action="/hub/deploy" class="w-full lg:w-auto">
        @csrf
        <button type="submit" class="w-full lg:w-auto bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 px-6 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
            Pull & Actualizar API
        </button>
    </form>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-10">
    <div class="glass-panel p-4 lg:p-6 rounded-2xl lg:rounded-3xl flex items-center gap-4 lg:gap-5 overflow-hidden">
        <div class="w-12 h-12 lg:w-14 lg:h-14 shrink-0 rounded-xl lg:rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center">
            <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </div>
        <div class="min-w-0">
            <p class="text-gray-400 text-xs lg:text-sm font-medium truncate">Usuarios Registrados</p>
            <h3 class="text-2xl lg:text-3xl font-bold text-white truncate">{{ $stats['total_users'] }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-4 lg:p-6 rounded-2xl lg:rounded-3xl flex items-center gap-4 lg:gap-5 overflow-hidden">
        <div class="w-12 h-12 lg:w-14 lg:h-14 shrink-0 rounded-xl lg:rounded-2xl bg-purple-500/20 text-purple-400 flex items-center justify-center">
            <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
        </div>
        <div class="min-w-0">
            <p class="text-gray-400 text-xs lg:text-sm font-medium truncate">Apps Detectadas</p>
            <h3 class="text-2xl lg:text-3xl font-bold text-white truncate">{{ $stats['total_apps'] }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-4 lg:p-6 rounded-2xl lg:rounded-3xl flex items-center gap-4 lg:gap-5 overflow-hidden">
        <div class="w-12 h-12 lg:w-14 lg:h-14 shrink-0 rounded-xl lg:rounded-2xl bg-green-500/20 text-green-400 flex items-center justify-center">
            <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div class="min-w-0">
            <p class="text-gray-400 text-xs lg:text-sm font-medium truncate">Estado del Servidor</p>
            <h3 class="text-2xl lg:text-3xl font-bold text-green-400 truncate">Online</h3>
        </div>
    </div>
</div>

<!-- Apps Grid -->
<h2 class="text-2xl font-bold text-white mb-6">Aplicaciones Activas</h2>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($apps as $app)
        <a href="/hub/app/{{ $app['file'] }}" class="block glass-panel p-6 rounded-3xl hover:border-indigo-500/50 hover:bg-white/5 transition-all group cursor-pointer">
            <div class="flex justify-between items-start mb-6">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <span class="bg-green-500/20 text-green-400 text-xs font-bold px-3 py-1 rounded-full border border-green-500/20">
                    {{ $app['status'] }}
                </span>
            </div>
            
            <h3 class="text-xl font-bold text-white mb-1 group-hover:text-indigo-400 transition-colors">{{ $app['name'] }}</h3>
            <p class="text-gray-500 text-sm mb-4">Módulo de rutas: <code class="text-xs bg-gray-900 px-2 py-1 rounded">{{ $app['file'] }}</code></p>
            
            <div class="pt-4 border-t border-gray-800 flex justify-between items-center">
                <span class="text-sm text-gray-400">~{{ $app['routes_count'] }} Rutas Registradas</span>
                <svg class="w-5 h-5 text-indigo-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
        </a>
    @endforeach
</div>
@endsection
