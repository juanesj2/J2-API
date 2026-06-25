@extends('hub.layout')

@section('title', 'Base de Datos - ' . $dbName)

@section('content')
<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Base de Datos</h1>
        <p class="text-gray-400 text-sm md:text-base">Explorador de la base de datos <code>{{ $dbName }}</code>.</p>
    </div>
</div>

<div x-data="{ search: '', activeCategory: 'Todas' }">
    <div class="mb-6 relative max-w-md">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>
        <input x-model="search" type="text" class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-10 pr-4 py-3 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" placeholder="Buscar tabla...">
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
</div>
@endsection
