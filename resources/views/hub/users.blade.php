@extends('hub.layout')

@section('title', 'Usuarios Globales')

@section('content')
<div class="mb-10">
    <h1 class="text-4xl font-extrabold text-white mb-2">Usuarios del Ecosistema</h1>
    <p class="text-gray-400">Listado centralizado de todos los usuarios registrados en tus aplicaciones.</p>
</div>

<div class="glass-panel rounded-3xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-900/50 border-b border-gray-800 text-sm font-semibold text-gray-400 uppercase tracking-wider">
                    <th class="p-5">Usuario</th>
                    <th class="p-5">Rol</th>
                    <th class="p-5">Registro</th>
                    <th class="p-5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($users as $user)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->profile_photo_path ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=312e81&color=fff' }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <p class="font-bold text-white">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-5">
                            @if($user->rol === 'SuperAdmin')
                                <span class="bg-indigo-500/20 text-indigo-400 text-xs font-bold px-3 py-1 rounded-full border border-indigo-500/20">SuperAdmin</span>
                            @elseif($user->rol === 'admin')
                                <span class="bg-purple-500/20 text-purple-400 text-xs font-bold px-3 py-1 rounded-full border border-purple-500/20">Admin</span>
                            @else
                                <span class="bg-gray-800 text-gray-300 text-xs font-bold px-3 py-1 rounded-full border border-gray-700">Usuario</span>
                            @endif
                        </td>
                        <td class="p-5 text-gray-400 text-sm">
                            {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'Desconocido' }}
                        </td>
                        <td class="p-5 text-right relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="text-gray-500 hover:text-white transition-colors p-2 rounded-lg hover:bg-gray-800">
                                <svg class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                            </button>
                            
                            <div x-show="open" x-transition.opacity class="absolute right-8 top-10 mt-2 w-48 bg-gray-900 border border-gray-700 rounded-xl shadow-xl z-50 overflow-hidden text-left" style="display: none;">
                                @if($user->id !== Auth::id() && $user->rol !== 'SuperAdmin')
                                    <form method="POST" action="/hub/usuarios/{{ $user->id }}/role">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-3 text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                                            {{ $user->rol === 'admin' ? 'Quitar rol Admin' : 'Hacer Admin' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="/hub/usuarios/{{ $user->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('¿Seguro que quieres eliminar este usuario?');" class="w-full text-left px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 transition-colors">
                                            Eliminar usuario
                                        </button>
                                    </form>
                                @else
                                    <div class="px-4 py-3 text-sm text-gray-500 italic">No hay acciones disponibles.</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="p-5 border-t border-gray-800 bg-gray-900/30">
        {{ $users->links() }}
    </div>
</div>
@endsection
