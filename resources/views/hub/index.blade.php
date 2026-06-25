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
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6 mb-6">
    <div class="glass-panel p-4 lg:p-6 rounded-2xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </div>
        <div class="min-w-0">
            <p class="text-gray-400 text-xs font-medium truncate">Usuarios Registrados</p>
            <h3 class="text-2xl font-bold text-white truncate">{{ $stats['total_users'] }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-4 lg:p-6 rounded-2xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
        </div>
        <div class="min-w-0">
            <p class="text-gray-400 text-xs font-medium truncate">Apps Detectadas</p>
            <h3 class="text-2xl font-bold text-white truncate">{{ $stats['total_apps'] }}</h3>
        </div>
    </div>
    
    <div class="glass-panel p-4 lg:p-6 rounded-2xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-500/20 text-green-400 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
        </div>
        <div class="w-full min-w-0">
            <div class="flex justify-between items-end mb-1">
                <p class="text-gray-400 text-xs font-medium truncate">Disco Usado</p>
                <span class="text-[10px] text-gray-300">{{ $stats['disk_used_gb'] }}/{{ $stats['disk_total_gb'] }} GB</span>
            </div>
            <div class="w-full bg-gray-700/50 rounded-full h-1.5 mb-1">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $stats['disk_usage_percent'] }}%"></div>
            </div>
            <h3 class="text-lg font-bold text-white truncate">{{ $stats['disk_usage_percent'] }}%</h3>
        </div>
    </div>

    <div class="glass-panel p-4 lg:p-6 rounded-2xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
        </div>
        <div class="min-w-0">
            <p class="text-gray-400 text-xs font-medium truncate">BD (MySQL)</p>
            <h3 class="text-2xl font-bold text-white truncate">{{ $stats['db_size_mb'] }} <span class="text-sm font-normal text-gray-400">MB</span></h3>
        </div>
    </div>
</div>

<!-- Info Server -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
    <div class="glass-panel p-3 text-center rounded-xl">
        <p class="text-gray-500 text-xs mb-1">PHP Version</p>
        <p class="text-white font-mono text-sm">{{ $stats['php_version'] }}</p>
    </div>
    <div class="glass-panel p-3 text-center rounded-xl">
        <p class="text-gray-500 text-xs mb-1">Laravel</p>
        <p class="text-white font-mono text-sm">{{ $stats['laravel_version'] }}</p>
    </div>
    <div class="glass-panel p-3 text-center rounded-xl">
        <p class="text-gray-500 text-xs mb-1">Entorno</p>
        <p class="text-white font-mono text-sm capitalize">{{ $stats['env'] }}</p>
    </div>
    <div class="glass-panel p-3 text-center rounded-xl">
        <p class="text-gray-500 text-xs mb-1">Status</p>
        <p class="text-green-400 font-bold text-sm">● Online</p>
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
