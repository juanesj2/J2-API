<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\User;

class HubController extends Controller
{
    /**
     * Mostrar la pantalla de Login del Hub
     */
    public function login()
    {
        if (Auth::check() && Auth::user()->rol === 'SuperAdmin') {
            return redirect('/hub');
        }
        return view('hub.login');
    }

    /**
     * Procesar el Login
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Siempre recordar la sesión para no tener que iniciar sesión cada vez
        if (Auth::attempt($credentials, true)) {
            if (Auth::user()->rol === 'SuperAdmin' || Auth::user()->rol === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended('/hub');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'No tienes permisos de SuperAdmin para acceder al Hub.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cerrar Sesión del Hub
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/hub/login');
    }

    /**
     * Dashboard Principal (Detectar Apps)
     */
    public function index()
    {
        // Detectar Apps leyendo la carpeta routes
        $routeFiles = File::files(base_path('routes'));
        $apps = [];

        foreach ($routeFiles as $file) {
            $filename = $file->getFilename();
            // Buscar archivos que empiecen por api_ y que no sean api_common
            if (str_starts_with($filename, 'api_') && $filename !== 'api_common.php') {
                // Limpiar nombre: api_enfoca.php -> Enfoca
                $name = str_replace(['api_', '.php'], '', $filename);
                $name = ucwords(str_replace('_', ' ', $name));

                // Leer el archivo para contar cuántas rutas (aproximado) tiene
                $content = file_get_contents($file->getPathname());
                $routeCount = substr_count($content, 'Route::');

                $apps[] = [
                    'name' => $name,
                    'file' => $filename,
                    'routes_count' => $routeCount,
                    'status' => 'Activa'
                ];
            }
        }

        // Estadísticas rápidas
        $stats = [
            'total_users' => User::count(),
            'total_apps' => count($apps),
        ];

        return view('hub.index', compact('apps', 'stats'));
    }

    /**
     * Gestión de Usuarios
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Búsqueda por nombre o email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtrado por App
        if ($request->filled('app')) {
            $query->where('app', $request->input('app'));
        }

        // Obtener la lista de apps dinámicamente desde la BD para el filtro
        $availableApps = User::select('app')->distinct()->whereNotNull('app')->where('app', '!=', '')->pluck('app');

        // Paginar usuarios de 15 en 15, conservando los parámetros de búsqueda
        $users = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->all());

        return view('hub.users', compact('users', 'availableApps'));
    }

    public function toggleRole(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }
        $user->rol = $user->rol === 'admin' ? 'usuario' : 'admin';
        $user->save();
        return back()->with('success', 'Rol actualizado correctamente.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }
        $user->delete();
        return back()->with('success', 'Usuario eliminado permanentemente.');
    }

    /**
     * Ver detalles de la app
     */
    public function showApp($file)
    {
        $path = base_path('routes/' . $file);
        if (!File::exists($path) || !str_starts_with($file, 'api_')) {
            abort(404);
        }

        $content = file_get_contents($path);
        
        // Extraer rutas de forma básica
        preg_match_all("/Route::(get|post|put|patch|delete|any|match)\s*\(\s*['\"]([^'\"]+)['\"]/i", $content, $matches);
        
        $routes = [];
        foreach($matches[1] as $index => $method) {
            $routes[] = [
                'method' => strtoupper($method),
                'uri' => $matches[2][$index]
            ];
        }

        $appName = ucwords(str_replace(['api_', '.php', '_'], ['', '', ' '], $file));
        
        return view('hub.app', compact('appName', 'routes', 'file', 'content'));
    }

    /**
     * Ejecutar Deploy desde GitHub
     */
    public function deploy()
    {
        // Solo para administradores desde la interfaz
        $output = [];
        
        // Ejecutar git pull
        exec('git pull origin main 2>&1', $pullOutput, $pullCode);
        $output['pull'] = implode("\n", $pullOutput);

        // Ejecutar migraciones de forma nativa para evitar cuelgues
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output['migrate'] = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Exception $e) {
            $output['migrate'] = "Error: " . $e->getMessage();
        }

        // Limpiar cachés de forma nativa
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            $output['optimize'] = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Exception $e) {
            $output['optimize'] = "Error: " . $e->getMessage();
        }

        return view('hub.deploy', compact('output'));
    }
}
