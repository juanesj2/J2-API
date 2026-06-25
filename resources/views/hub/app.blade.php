@extends('hub.layout')

@section('title', $appName)

@section('content')
<div class="mb-10 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-extrabold text-white mb-2">{{ $appName }}</h1>
        <p class="text-gray-400">Rutas detectadas en <code>{{ $file }}</code></p>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('hub.app.export', $file) }}" class="text-sm bg-green-600/20 text-green-400 hover:bg-green-500 hover:text-white border border-green-500/50 transition-all font-bold py-2 px-4 rounded-xl flex items-center gap-2 shadow-lg">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            Exportar Postman
        </a>
        <a href="/hub" class="text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Volver
        </a>
    </div>
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
                    <div x-data="{ 
                            open: false, 
                            endpoint: '/api' + '{{ str_starts_with($route['uri'], '/') ? $route['uri'] : '/'.$route['uri'] }}', 
                            body: '', 
                            response: null, 
                            status: null, 
                            loading: false, 
                            execute() { 
                                this.loading = true;
                                let options = { method: '{{ $route['method'] }}', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' } };
                                if('{{ $route['method'] }}' !== 'GET' && this.body) options.body = this.body;
                                fetch(this.endpoint, options)
                                .then(async r => { this.status = r.status; this.response = JSON.stringify(await r.json(), null, 2); this.loading = false; })
                                .catch(e => { this.status = 'Error'; this.response = e.toString(); this.loading = false; });
                            }
                         }"
                         x-show="methodFilter === 'ALL' || methodFilter === '{{ $route['method'] }}' || (methodFilter === 'PUT' && '{{ $route['method'] }}' === 'PATCH')"
                         x-transition.opacity
                         class="bg-gray-900/50 border border-gray-800 rounded-xl p-3 flex flex-col gap-2">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                            <div class="flex items-center gap-3">
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
                            <button class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1 rounded-lg transition-colors flex items-center gap-1">
                                <svg x-show="!open" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <svg x-show="open" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                Probar
                            </button>
                        </div>
                        
                        <!-- Body (Mini-Postman) -->
                        <div x-show="open" class="pt-4 border-t border-gray-800 mt-2" x-transition>
                            <div class="mb-3">
                                <label class="text-xs text-gray-500 mb-1 block">URL Final</label>
                                <input type="text" x-model="endpoint" class="w-full bg-black/50 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white font-mono focus:border-indigo-500 outline-none">
                            </div>
                            
                            <template x-if="'{{ $route['method'] }}' !== 'GET'">
                                <div class="mb-3">
                                    <label class="text-xs text-gray-500 mb-1 block">Body (JSON)</label>
                                    <textarea x-model="body" rows="3" class="w-full bg-black/50 border border-gray-700 rounded-lg px-3 py-2 text-sm text-green-400 font-mono focus:border-indigo-500 outline-none custom-scrollbar" placeholder='{"key": "value"}' spellcheck="false"></textarea>
                                </div>
                            </template>
                            
                            <div class="flex justify-end mb-4">
                                <button @click="execute" class="bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-bold py-1.5 px-4 rounded-lg flex items-center gap-2 transition-colors">
                                    <span x-show="!loading">Enviar Petición</span>
                                    <span x-show="loading" class="animate-pulse">Enviando...</span>
                                </button>
                            </div>
                            
                            <template x-if="status">
                                <div class="bg-gray-950 border border-gray-800 rounded-lg p-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-gray-400">Response</span>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded" :class="status >= 200 && status < 300 ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'" x-text="'Status: ' + status"></span>
                                    </div>
                                    <pre class="text-xs text-indigo-300 font-mono overflow-auto max-h-64 custom-scrollbar whitespace-pre-wrap" x-text="response"></pre>
                                </div>
                            </template>
                        </div>
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
