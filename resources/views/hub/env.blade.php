@extends('hub.layout')

@section('title', 'Configuración de Entorno (.env)')

@section('content')
<div class="mb-10">
    <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Editor de Entorno</h1>
    <p class="text-gray-400 text-sm md:text-base">Visualiza y modifica las variables del archivo <code>.env</code> de J2-API.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-green-500/20 border border-green-500/50 text-green-400 font-medium">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-red-500/20 border border-red-500/50 text-red-400 font-medium">
        {{ session('error') }}
    </div>
@endif

<div class="glass-panel p-6 rounded-3xl shadow-xl">
    @if(!$hasAccess)
        <div class="max-w-md mx-auto py-10 text-center">
            <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Zona Protegida</h2>
            <p class="text-gray-400 mb-8">Por seguridad, debes confirmar tu contraseña de administrador para ver o modificar este archivo crítico.</p>
            
            <form method="POST" action="/hub/env/verify" class="space-y-4">
                @csrf
                <div>
                    <input type="password" name="password" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" placeholder="Tu contraseña de J2 Hub">
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition-colors">
                    Desbloquear Entorno
                </button>
            </form>
        </div>
    @else
        <div class="mb-6 flex justify-between items-center" x-data="sessionTimer({{ $unlockedAt }})">
            <div class="flex items-center gap-3 bg-gray-900 border border-gray-800 rounded-xl p-2 px-4 shadow-lg">
                <div class="flex flex-col">
                    <span class="text-xs text-gray-400 font-bold uppercase">Sesión .ENV</span>
                    <span class="text-sm font-mono text-green-400" x-text="timeLeftText"></span>
                </div>
                <form method="POST" action="/hub/env/extend" class="ml-2 pl-3 border-l border-gray-700">
                    @csrf
                    <button type="submit" class="text-xs font-bold px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg transition-colors flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        +2H
                    </button>
                </form>
            </div>
        </div>
        
        <form method="POST" action="/hub/env/update">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-400 text-sm font-bold mb-2" for="env_content">
                    Contenido de <code>.env</code>
                </label>
                <textarea 
                    name="env_content" 
                    id="env_content" 
                    rows="20" 
                    class="w-full bg-gray-950 border border-gray-700 rounded-xl px-4 py-3 text-gray-300 font-mono text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors custom-scrollbar" 
                    spellcheck="false"
                >{{ $envContent }}</textarea>
                <p class="text-xs text-yellow-500 mt-2">
                    <span class="font-bold">⚠️ Precaución:</span> Un error de sintaxis en este archivo puede hacer que toda la API deje de funcionar.
                </p>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
    @endif
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(17, 24, 39, 1); 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(75, 85, 99, 0.8); 
        border-radius: 4px;
    }
</style>
@endsection
