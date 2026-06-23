@extends('hub.layout')

@section('title', $appName)

@section('content')
<div class="mb-10 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-extrabold text-white mb-2">{{ $appName }}</h1>
        <p class="text-gray-400">Rutas detectadas en <code>{{ $file }}</code></p>
    </div>
    <a href="/hub" class="text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Volver
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Lista de Rutas -->
    <div x-data="{ methodFilter: 'ALL' }" class="glass-panel rounded-3xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-white">Endpoints Detectados</h2>
            <div class="flex gap-2 overflow-x-auto pb-2 sm:pb-0 custom-scrollbar">
                <button @click="methodFilter = 'ALL'" :class="methodFilter === 'ALL' ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-gray-800/50 text-gray-400 hover:text-white hover:bg-gray-700'" class="px-3 py-1 text-xs font-bold rounded-lg transition-colors border border-gray-700">TODO</button>
                <button @click="methodFilter = 'GET'" :class="methodFilter === 'GET' ? 'bg-blue-500/20 text-blue-400 border-blue-500/50' : 'bg-gray-800/50 text-gray-400 hover:text-blue-400 hover:bg-gray-700'" class="px-3 py-1 text-xs font-bold rounded-lg transition-colors border border-gray-700">GET</button>
                <button @click="methodFilter = 'POST'" :class="methodFilter === 'POST' ? 'bg-green-500/20 text-green-400 border-green-500/50' : 'bg-gray-800/50 text-gray-400 hover:text-green-400 hover:bg-gray-700'" class="px-3 py-1 text-xs font-bold rounded-lg transition-colors border border-gray-700">POST</button>
                <button @click="methodFilter = 'PUT'" :class="methodFilter === 'PUT' ? 'bg-yellow-500/20 text-yellow-400 border-yellow-500/50' : 'bg-gray-800/50 text-gray-400 hover:text-yellow-400 hover:bg-gray-700'" class="px-3 py-1 text-xs font-bold rounded-lg transition-colors border border-gray-700">PUT</button>
                <button @click="methodFilter = 'DELETE'" :class="methodFilter === 'DELETE' ? 'bg-red-500/20 text-red-400 border-red-500/50' : 'bg-gray-800/50 text-gray-400 hover:text-red-400 hover:bg-gray-700'" class="px-3 py-1 text-xs font-bold rounded-lg transition-colors border border-gray-700">DELETE</button>
            </div>
        </div>

        @if(count($routes) > 0)
            <div class="space-y-3">
                @foreach($routes as $route)
                    <div x-show="methodFilter === 'ALL' || methodFilter === '{{ $route['method'] }}' || (methodFilter === 'PUT' && '{{ $route['method'] }}' === 'PATCH')"
                         x-transition.opacity
                         class="bg-gray-900/50 border border-gray-800 rounded-xl p-3 flex items-center gap-3">
                        @php
                            $methodColor = match($route['method']) {
                                'GET' => 'text-blue-400 bg-blue-500/10 border-blue-500/20',
                                'POST' => 'text-green-400 bg-green-500/10 border-green-500/20',
                                'PUT', 'PATCH' => 'text-yellow-400 bg-yellow-500/10 border-yellow-500/20',
                                'DELETE' => 'text-red-400 bg-red-500/10 border-red-500/20',
                                default => 'text-gray-400 bg-gray-500/10 border-gray-500/20',
                            };
                        @endphp
                        <span class="text-xs font-bold px-2 py-1 rounded border {{ $methodColor }} w-16 text-center shrink-0">
                            {{ $route['method'] }}
                        </span>
                        <span class="text-gray-300 font-mono text-xs md:text-sm break-all">{{ $route['uri'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 italic">No se pudieron detectar rutas automáticamente en este archivo.</p>
        @endif
    </div>

    <!-- Código del Archivo -->
    <div class="glass-panel rounded-3xl p-6 flex flex-col">
        <h2 class="text-xl font-bold text-white mb-4">Código Fuente</h2>
        <div class="bg-black/50 rounded-xl p-4 overflow-auto flex-1">
            <pre class="text-indigo-300 font-mono text-sm whitespace-pre-wrap">{{ $content }}</pre>
        </div>
    </div>
</div>
@endsection
