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

        // Métricas del servidor
        $diskTotal = disk_total_space(base_path());
        $diskFree = disk_free_space(base_path());
        $diskUsed = $diskTotal - $diskFree;
        $diskUsagePercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 2) : 0;

        $dbName = config('database.connections.mysql.database');
        $dbSizeQuery = \Illuminate\Support\Facades\DB::select("
            SELECT SUM(data_length + index_length) / 1024 / 1024 AS size_mb 
            FROM information_schema.TABLES 
            WHERE table_schema = ?
        ", [$dbName]);
        $dbSize = $dbSizeQuery[0]->size_mb ?? 0;

        // Estadísticas rápidas
        $stats = [
            'total_users' => User::count(),
            'total_apps' => count($apps),
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'env' => app()->environment(),
            'disk_total_gb' => round($diskTotal / 1024 / 1024 / 1024, 2),
            'disk_used_gb' => round($diskUsed / 1024 / 1024 / 1024, 2),
            'disk_free_gb' => round($diskFree / 1024 / 1024 / 1024, 2),
            'disk_usage_percent' => $diskUsagePercent,
            'db_size_mb' => round($dbSize, 2),
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

    public function exportCollection($file)
    {
        $path = base_path('routes/' . $file);
        if (!File::exists($path) || !str_starts_with($file, 'api_')) {
            abort(404);
        }

        $content = File::get($path);
        preg_match_all("/Route::(get|post|put|patch|delete)\(\s*['\"]([^'\"]+)['\"]/", $content, $matches);
        
        $routes = [];
        if (!empty($matches[0])) {
            foreach ($matches[1] as $index => $method) {
                $routes[] = [
                    'method' => strtoupper($method),
                    'uri' => $matches[2][$index]
                ];
            }
        }

        $appName = ucwords(str_replace(['api_', '.php', '_'], ['', '', ' '], $file));
        
        $postman = [
            'info' => [
                'name' => "J2 API - {$appName}",
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
            ],
            'item' => []
        ];

        foreach ($routes as $route) {
            $uri = str_starts_with($route['uri'], '/') ? ltrim($route['uri'], '/') : $route['uri'];
            $postman['item'][] = [
                'name' => "[{$route['method']}] {$uri}",
                'request' => [
                    'method' => $route['method'],
                    'header' => [
                        ['key' => 'Accept', 'value' => 'application/json', 'type' => 'text']
                    ],
                    'url' => [
                        'raw' => "{{base_url}}/api/{$uri}",
                        'host' => ['{{base_url}}'],
                        'path' => array_merge(['api'], array_filter(explode('/', $uri)))
                    ]
                ]
            ];
        }

        return response()->json($postman)->header('Content-Disposition', 'attachment; filename="postman_collection_' . strtolower(str_replace(' ', '_', $appName)) . '.json"');
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

    /**
     * Log Viewer
     */
    public function logs()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            $content = File::get($logPath);
            preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*?(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|$)/s', $content, $matches);
            
            if (!empty($matches[0])) {
                $logs = array_reverse($matches[0]); // Most recent first
                $logs = array_slice($logs, 0, 200); // Limit to 200
            } else if (!empty(trim($content))) {
                $logs = array_slice(array_reverse(explode("\n", trim($content))), 0, 200);
            }
        }

        return view('hub.logs', compact('logs'));
    }

    public function clearLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }
        return back()->with('success', 'Logs limpiados correctamente.');
    }

    /**
     * Env Editor
     */
    public function envEditor()
    {
        $unlockedAt = session('env_unlocked_at');
        $hasAccess = $unlockedAt && now()->timestamp - $unlockedAt < 7200;
        
        $envContent = '';
        if ($hasAccess && File::exists(base_path('.env'))) {
            $envContent = File::get(base_path('.env'));
        }

        return view('hub.env', compact('hasAccess', 'envContent', 'unlockedAt'));
    }

    public function verifyEnvPassword(Request $request)
    {
        $request->validate(['password' => 'required']);
        
        if (Auth::attempt(['email' => Auth::user()->email, 'password' => $request->password])) {
            session(['env_unlocked_at' => now()->timestamp]);
            return back()->with('success', 'Contraseña verificada. Tienes acceso al archivo .env por 2 horas.');
        }

        return back()->with('error', 'Contraseña incorrecta.');
    }

    public function updateEnv(Request $request)
    {
        $unlockedAt = session('env_unlocked_at');
        if (!$unlockedAt || now()->timestamp - $unlockedAt >= 7200) {
            return back()->with('error', 'Acceso denegado o sesión caducada. Verifica tu contraseña primero.');
        }

        $request->validate(['env_content' => 'required']);
        File::put(base_path('.env'), $request->env_content);
        
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
        } catch (\Exception $e) {}

        return back()->with('success', 'Archivo .env actualizado correctamente.');
    }

    public function extendEnvSession(Request $request)
    {
        $unlockedAt = session('env_unlocked_at');
        if ($unlockedAt && (now()->timestamp - $unlockedAt) < 7200) {
            session(['env_unlocked_at' => now()->timestamp]);
            return redirect()->back()->with('success', 'Sesión del entorno extendida por 2 horas más.');
        }

        return redirect()->route('hub.env')->with('error', 'La sesión ha expirado o no está desbloqueada.');
    }
}
