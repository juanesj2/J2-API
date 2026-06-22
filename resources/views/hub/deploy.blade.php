@extends('hub.layout')

@section('title', 'Deploy Console')

@section('content')
<div class="mb-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-4xl font-extrabold text-white mb-2">Despliegue Finalizado</h1>
        <p class="text-gray-400 text-sm md:text-base">Los comandos de actualización se han ejecutado en el servidor.</p>
    </div>
    <a href="/hub" class="text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-2 whitespace-nowrap">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Volver al Dashboard
    </a>
</div>

<div class="space-y-6">
    <!-- GIT PULL -->
    <div class="glass-panel rounded-2xl overflow-hidden">
        <div class="bg-gray-900/80 px-4 py-3 border-b border-gray-800 flex items-center gap-2">
            <div class="flex gap-1.5">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
            </div>
            <span class="text-gray-400 text-sm font-mono ml-2">$ git pull origin main</span>
        </div>
        <div class="p-4 bg-black/50 text-green-400 font-mono text-sm overflow-x-auto whitespace-pre-wrap">
{{ $output['pull'] ?: 'No output / Success' }}
        </div>
    </div>

    <!-- PHP ARTISAN MIGRATE -->
    <div class="glass-panel rounded-2xl overflow-hidden">
        <div class="bg-gray-900/80 px-4 py-3 border-b border-gray-800 flex items-center gap-2">
            <div class="flex gap-1.5">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
            </div>
            <span class="text-gray-400 text-sm font-mono ml-2">$ php artisan migrate --force</span>
        </div>
        <div class="p-4 bg-black/50 text-indigo-300 font-mono text-sm overflow-x-auto whitespace-pre-wrap">
{{ $output['migrate'] ?: 'No output / Success' }}
        </div>
    </div>

    <!-- PHP ARTISAN OPTIMIZE:CLEAR -->
    <div class="glass-panel rounded-2xl overflow-hidden">
        <div class="bg-gray-900/80 px-4 py-3 border-b border-gray-800 flex items-center gap-2">
            <div class="flex gap-1.5">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
            </div>
            <span class="text-gray-400 text-sm font-mono ml-2">$ php artisan optimize:clear</span>
        </div>
        <div class="p-4 bg-black/50 text-yellow-300 font-mono text-sm overflow-x-auto whitespace-pre-wrap">
{{ $output['optimize'] ?: 'No output / Success' }}
        </div>
    </div>
</div>
@endsection
