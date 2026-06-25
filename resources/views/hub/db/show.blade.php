@extends('hub.layout')

@section('title', 'Tabla - ' . $table)

@section('content')
<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('hub.db.index') }}" class="text-indigo-400 hover:text-indigo-300 transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white">{{ $table }}</h1>
        </div>
        <p class="text-gray-400 text-sm md:text-base ml-9">Mostrando {{ $records->count() }} de {{ $records->total() }} registros.</p>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-green-900/50 border-l-4 border-green-500 p-4 rounded-r-xl">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-green-300">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-900/50 border-l-4 border-red-500 p-4 rounded-r-xl">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-red-300">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<div x-data="{ openInsert: false }" class="mb-8">
    <div class="flex justify-end mb-4">
        <button @click="openInsert = !openInsert" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl flex items-center gap-2 transition-all shadow-lg shadow-indigo-500/20">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <span x-text="openInsert ? 'Cancelar' : 'Insertar Registro'"></span>
        </button>
    </div>

    <div x-show="openInsert" x-collapse x-cloak>
        <div class="glass-panel p-6 rounded-3xl mb-6 border border-indigo-500/30">
            <h3 class="text-xl font-bold text-white mb-4">Añadir nuevo registro</h3>
            
            @if(!$hasAccess)
                <div class="bg-indigo-900/30 p-6 rounded-2xl border border-indigo-500/20 text-center max-w-lg mx-auto">
                    <svg class="w-12 h-12 text-indigo-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" /></svg>
                    <h4 class="text-lg font-bold text-white mb-2">Seguridad de Base de Datos</h4>
                    <p class="text-gray-400 text-sm mb-6">Por motivos de seguridad, introduce tu contraseña de administrador para desbloquear la inserción de datos.</p>
                    
                    <form action="{{ route('hub.db.verify') }}" method="POST">
                        @csrf
                        <input type="password" name="password" required class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 mb-4 text-center" placeholder="Tu contraseña">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-indigo-500/20">
                            Desbloquear
                        </button>
                    </form>
                </div>
            @else
                <form action="{{ route('hub.db.insert', $table) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($columns as $col)
                            @if($col !== 'id' && $col !== 'created_at' && $col !== 'updated_at')
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">{{ $col }}</label>
                                    <input type="text" name="{{ $col }}" class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" placeholder="Valor (opcional)">
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-xl transition-all shadow-lg shadow-green-500/20 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Guardar Registro
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<div class="glass-panel rounded-3xl shadow-xl overflow-hidden border border-gray-800">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="text-xs text-gray-400 uppercase bg-gray-900/50 border-b border-gray-800">
                <tr>
                    @foreach($columns as $col)
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">
                            {{ $col }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($records as $row)
                    <tr class="hover:bg-white/5 transition-colors">
                        @foreach($columns as $col)
                            <td class="px-6 py-3 whitespace-nowrap">
                                @php
                                    $val = $row->$col;
                                @endphp
                                @if(is_null($val))
                                    <span class="text-gray-500 italic">NULL</span>
                                @elseif(strlen((string)$val) > 50)
                                    <span title="{{ $val }}" class="cursor-help border-b border-dashed border-gray-600">{{ substr((string)$val, 0, 50) }}...</span>
                                @else
                                    {{ $val }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-6 py-8 text-center text-gray-500">
                            No hay registros en esta tabla.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($records->hasPages())
        <div class="p-4 border-t border-gray-800 bg-gray-900/30">
            {{ $records->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(17, 24, 39, 1); 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(75, 85, 99, 0.8); 
        border-radius: 4px;
    }
</style>
@endsection
