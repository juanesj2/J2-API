@extends('hub.layout')

@section('title', 'Base de Datos - ' . $dbName)

@section('content')
<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Base de Datos</h1>
        <p class="text-gray-400 text-sm md:text-base">Explorador de la base de datos <code>{{ $dbName }}</code>.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($tableData as $table)
        <a href="{{ route('hub.db.show', $table['name']) }}" class="block glass-panel p-6 rounded-3xl hover:border-indigo-500/50 hover:bg-white/5 transition-all group cursor-pointer">
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
@endsection
