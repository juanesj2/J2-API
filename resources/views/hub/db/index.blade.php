@extends('hub.layout')

@section('title', 'Base de Datos - ' . $dbName)

@section('content')
<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Base de Datos</h1>
        <p class="text-gray-400 text-sm md:text-base">Explorador de la base de datos <code>{{ $dbName }}</code>.</p>
    </div>
    
    @if($hasAccess)
        <div x-data="sessionTimer({{ $unlockedAt }})" class="flex items-center gap-3 bg-gray-900 border border-gray-800 rounded-xl p-2 px-4 shadow-lg">
            <div class="flex flex-col">
                <span class="text-xs text-gray-400 font-bold uppercase">Sesión DB</span>
                <span class="text-sm font-mono text-green-400" x-text="timeLeftText"></span>
            </div>
            <form method="POST" action="/hub/db/extend" class="ml-2 pl-3 border-l border-gray-700">
                @csrf
                <button type="submit" class="text-xs font-bold px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    +2H
                </button>
            </form>
        </div>
    @endif
</div>

<div x-data="{ search: '', activeCategory: 'Todas', sqlModalOpen: false }">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="relative w-full md:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input x-model="search" type="text" class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-10 pr-4 py-3 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" placeholder="Buscar tabla...">
        </div>
        
        @if($hasAccess)
        <button @click="sqlModalOpen = true" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-purple-500/20 transition-all flex items-center gap-2 whitespace-nowrap border border-purple-500/30">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            Ejecutar SQL Raw
        </button>
        @endif
    </div>

    <!-- Filtros de Categorías -->
    <div class="flex gap-3 mb-8 overflow-x-auto pb-2 custom-scrollbar">
        @foreach($categories as $catName => $catCount)
            <button @click="activeCategory = '{{ $catName }}'" 
                    :class="activeCategory === '{{ $catName }}' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20 border-indigo-500' : 'bg-gray-800 text-gray-400 border-gray-700 hover:bg-gray-700 hover:text-white'"
                    class="px-5 py-2 rounded-full border font-medium text-sm transition-all whitespace-nowrap flex items-center gap-2">
                {{ $catName }}
                <span :class="activeCategory === '{{ $catName }}' ? 'bg-indigo-800 text-indigo-100' : 'bg-gray-900 text-gray-500'" class="px-2 py-0.5 rounded-full text-xs">
                    {{ $catCount }}
                </span>
            </button>
        @endforeach
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($tableData as $table)
            <a href="{{ route('hub.db.show', $table['name']) }}" 
               x-show="(activeCategory === 'Todas' || activeCategory === '{{ $table['category'] }}') && (search === '' || '{{ strtolower($table['name']) }}'.includes(search.toLowerCase()))"
               x-transition
               class="block glass-panel p-6 rounded-3xl hover:border-indigo-500/50 hover:bg-white/5 transition-all group cursor-pointer">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-600/20 flex items-center justify-center text-indigo-400 shadow-lg group-hover:text-indigo-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                    </div>
                </div>
                
                <h3 class="text-xl font-bold text-white mb-1 group-hover:text-indigo-400 transition-colors truncate" title="{{ $table['name'] }}">{{ $table['name'] }}</h3>
                
                <div class="pt-4 border-t border-gray-800 flex justify-between items-center">
                    <span class="text-sm text-gray-400">{{ number_format($table['count']) }} Registros</span>
                    <svg class="w-5 h-5 text-indigo-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>
        @endforeach
    </div>
    
    <div x-show="!Object.values({{ Js::from($tableData) }}).some(t => (activeCategory === 'Todas' || activeCategory === t.category) && t.name.toLowerCase().includes(search.toLowerCase()))" class="text-center py-12" style="display: none;">
        <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <p class="text-gray-400 text-lg">No se encontraron tablas para la categoría <span class="text-white font-bold" x-text="activeCategory"></span> <span x-show="search !== ''">que coincidan con "<span class="text-white font-bold" x-text="search"></span>"</span>.</p>
    </div>

    @if($hasAccess)
        <template x-teleport="body">
            <div x-show="sqlModalOpen" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="sqlModalOpen" x-transition.opacity class="fixed inset-0 bg-black/80 transition-opacity" aria-hidden="true" @click="sqlModalOpen = false"></div>
                    <div x-show="sqlModalOpen" x-transition class="relative transform overflow-hidden rounded-2xl bg-gray-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-purple-500/30 flex flex-col max-h-[90vh]">
                        <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center text-purple-400">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Ejecutor SQL Raw</h3>
                                    <p class="text-xs text-purple-400 font-mono">Modo Avanzado</p>
                                </div>
                            </div>
                            <button @click="sqlModalOpen = false" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <form :action="`/hub/db/sql`" method="POST" class="flex flex-col flex-1 overflow-hidden">
                            @csrf
                            <div class="p-6 bg-gray-950 flex-1 flex flex-col">
                                <p class="text-gray-400 text-sm mb-4">
                                    Introduce tu sentencia SQL. Las consultas <code>SELECT</code> ejecutarán e informarán de la cantidad de filas obtenidas, mientras que los comandos de alteración como <code>INSERT</code>, <code>UPDATE</code> o <code>DELETE</code> afectarán directamente a la base de datos. ¡Úsalo con precaución!
                                </p>
                                <textarea name="query" rows="12" class="w-full flex-1 bg-gray-900 border border-gray-700 rounded-xl p-4 text-green-400 font-mono text-sm focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors custom-scrollbar resize-none" placeholder="SELECT * FROM users WHERE id = 1;..." required></textarea>
                            </div>
                            <div class="px-6 py-4 bg-gray-800/50 border-t border-gray-800 flex justify-end gap-3">
                                <button type="button" @click="sqlModalOpen = false" class="px-4 py-2 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-colors font-medium">Cancelar</button>
                                <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold py-2 px-6 rounded-xl transition-all shadow-lg shadow-purple-500/20 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Ejecutar Consulta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    @endif
</div>
@endsection
