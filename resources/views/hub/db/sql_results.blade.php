@extends('hub.layout')

@section('title', 'Resultados SQL')

@section('content')
<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('hub.db.index') }}" class="text-indigo-400 hover:text-indigo-300 transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white">Resultados de la Consulta</h1>
        </div>
        <p class="text-gray-400 text-sm md:text-base ml-9">Mostrando {{ count($results) }} resultados.</p>
    </div>
    
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
</div>

<div class="glass-panel p-6 rounded-3xl mb-8">
    <h3 class="text-lg font-bold text-gray-400 mb-2">Consulta SQL Ejecutada</h3>
    <div class="bg-gray-950 border border-gray-800 rounded-xl p-4">
        <code class="text-purple-400 font-mono text-sm break-all">{{ $sql }}</code>
    </div>
</div>

<div class="glass-panel rounded-3xl overflow-hidden shadow-2xl">
    <div class="overflow-x-auto custom-scrollbar">
        @if(count($results) > 0)
            @php
                // Obtener las columnas del primer resultado
                $firstRow = (array) $results[0];
                $columns = array_keys($firstRow);
            @endphp
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        @foreach($columns as $col)
                            <th class="p-4 border-b border-gray-800 bg-gray-900/50 text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                {{ $col }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @foreach($results as $row)
                        <tr class="hover:bg-white/5 transition-colors">
                            @foreach($columns as $col)
                                <td class="p-4 text-sm text-gray-300 whitespace-nowrap">
                                    @php
                                        $val = ((array) $row)[$col];
                                    @endphp
                                    @if(is_null($val))
                                        <span class="text-gray-600 italic">NULL</span>
                                    @elseif(is_bool($val))
                                        <span class="{{ $val ? 'text-green-400' : 'text-red-400' }}">{{ $val ? 'true' : 'false' }}</span>
                                    @elseif(is_string($val) && strlen($val) > 50)
                                        <span title="{{ $val }}">{{ substr($val, 0, 50) }}...</span>
                                    @else
                                        {{ (string) $val }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                <p class="text-gray-400 text-lg">La consulta se ejecutó con éxito pero devolvió 0 filas.</p>
            </div>
        @endif
    </div>
</div>
@endsection
