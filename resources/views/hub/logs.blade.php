@extends('hub.layout')

@section('title', 'Server Logs')

@section('content')
<div class="mb-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Server Logs</h1>
        <p class="text-gray-400 text-sm md:text-base">Últimas 200 entradas del archivo <code>laravel.log</code>.</p>
    </div>
    
    <form method="POST" action="/hub/logs/clear" onsubmit="return confirm('¿Estás seguro de que quieres limpiar todos los logs?');">
        @csrf
        <button type="submit" class="bg-red-500/20 text-red-500 hover:bg-red-500 hover:text-white border border-red-500/50 font-semibold py-2 px-4 rounded-xl transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            Limpiar Logs
        </button>
    </form>
</div>

@if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-green-500/20 border border-green-500/50 text-green-400 font-medium flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        {{ session('success') }}
    </div>
@endif

<div class="glass-panel p-4 md:p-6 rounded-2xl md:rounded-3xl shadow-xl overflow-hidden flex flex-col h-[70vh]">
    @if(empty($logs))
        <div class="flex flex-col items-center justify-center h-full text-gray-500">
            <svg class="w-16 h-16 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            <p class="text-lg font-medium">El archivo de logs está vacío.</p>
            <p class="text-sm mt-1">No hay errores recientes registrados en el sistema.</p>
        </div>
    @else
        <div class="overflow-y-auto w-full h-full pr-2 space-y-3 custom-scrollbar">
            @foreach($logs as $log)
                @php
                    $isError = stripos($log, 'local.ERROR') !== false || stripos($log, 'Exception') !== false || stripos($log, 'Stack trace') !== false;
                    $isWarning = stripos($log, 'local.WARNING') !== false;
                    $bgColor = $isError ? 'bg-red-500/10 border-red-500/30' : ($isWarning ? 'bg-yellow-500/10 border-yellow-500/30' : 'bg-gray-800/50 border-gray-700/50');
                    $textColor = $isError ? 'text-red-400' : ($isWarning ? 'text-yellow-400' : 'text-gray-300');
                @endphp
                
                <div class="p-3 md:p-4 rounded-xl border {{ $bgColor }} font-mono text-xs md:text-sm {{ $textColor }} overflow-x-auto whitespace-pre-wrap">
                    {{ trim($log) }}
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(31, 41, 55, 0.5); 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(75, 85, 99, 0.8); 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(107, 114, 128, 1); 
    }
</style>
@endsection
