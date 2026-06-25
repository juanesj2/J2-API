<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J2 Hub - @yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Tailwind CSS (CDN para evitar compilar) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#6366f1',
                            600: '#4f46e5',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-panel {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex flex-col md:flex-row font-sans selection:bg-brand-500 selection:text-white">

    @auth
    <!-- Mobile Header -->
    <div class="md:hidden glass-panel border-b border-gray-800 p-4 flex justify-between items-center sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <img src="{{ asset('imagenes/logo_hub.png') }}" alt="J2 Hub Logo" class="w-8 h-8 shadow-lg">
            <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">HUB</span>
        </div>
        <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full'); document.getElementById('overlay').classList.toggle('hidden');" class="text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
    </div>

    <!-- Mobile Overlay -->
    <div id="overlay" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden');" class="fixed inset-0 bg-black/60 z-30 hidden md:hidden backdrop-blur-sm"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 glass-panel border-r border-gray-800 flex flex-col justify-between fixed h-full z-40 transition-transform duration-300 -translate-x-full md:translate-x-0">
        <div>
            <div class="p-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('imagenes/logo_hub.png') }}" alt="J2 Hub Logo" class="w-10 h-10 shadow-lg">
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">HUB</span>
                </div>
                <button onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); document.getElementById('overlay').classList.add('hidden');" class="md:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <nav class="mt-2 px-4 flex flex-col gap-2">
                <a href="/hub" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('hub') ? 'bg-indigo-500/20 text-indigo-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    Dashboard
                </a>
                <a href="/hub/usuarios" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('hub/usuarios*') ? 'bg-indigo-500/20 text-indigo-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    Usuarios Globales
                </a>
                <a href="/hub/logs" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('hub/logs*') ? 'bg-indigo-500/20 text-indigo-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                    Logs del Servidor
                </a>
                <a href="/hub/env" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('hub/env*') ? 'bg-indigo-500/20 text-indigo-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Configuración (.env)
                </a>
                <a href="/hub/db" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('hub/db*') ? 'bg-indigo-500/20 text-indigo-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                    Base de Datos
                </a>
            </nav>
        </div>

        <div class="p-4 mb-4">
            <div class="glass-panel rounded-2xl p-4 flex flex-col gap-3 text-sm border-gray-800">
                <div class="flex items-center gap-3">
                    <img src="{{ Auth::user()->profile_photo_path ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=4f46e5&color=fff' }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover shrink-0">
                    <div class="overflow-hidden">
                        <p class="font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-indigo-400 font-medium truncate">Super Admin</p>
                    </div>
                </div>
                <form method="POST" action="/hub/logout" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-center py-2 text-gray-400 hover:text-red-400 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </aside>
    @endauth

    <!-- Main Content -->
    <main class="flex-1 @auth md:ml-64 @endauth relative overflow-hidden w-full max-w-full">
        <!-- Background Orbs -->
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-indigo-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-20 pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-20 pointer-events-none"></div>

        <div class="relative z-10 p-5 md:p-10 max-w-7xl mx-auto min-h-screen flex flex-col">
            @if(session('error'))
                <div class="mb-8 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-3 animate-pulse">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

</body>
</html>
