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

        if (Auth::attempt($credentials)) {
            if (Auth::user()->rol === 'SuperAdmin') {
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
    public function users()
    {
        // Paginar usuarios de 15 en 15, ordenados por los más recientes
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('hub.users', compact('users'));
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

        // Ejecutar migraciones con PHP_BINARY (Evita errores si "php" no está en el PATH)
        exec(PHP_BINARY . ' artisan migrate --force 2>&1', $migrateOutput, $migrateCode);
        $output['migrate'] = implode("\n", $migrateOutput);

        // Limpiar cachés
        exec(PHP_BINARY . ' artisan optimize:clear 2>&1', $optimizeOutput, $optimizeCode);
        $output['optimize'] = implode("\n", $optimizeOutput);

        return view('hub.deploy', compact('output'));
    }
}
