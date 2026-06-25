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
