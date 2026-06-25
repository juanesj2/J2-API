<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HubDbController extends Controller
{
    public function index()
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tableKey = "Tables_in_{$dbName}";
        
        $systemTables = ['cache', 'cache_locks', 'failed_jobs', 'jobs', 'job_batches', 'migrations', 'password_reset_tokens', 'personal_access_tokens', 'sessions', 'users'];
        
        $tableData = [];
        $categories = ['Todas' => 0, 'Enfoca' => 0, 'Love Widget' => 0, 'System' => 0, 'General' => 0];

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $count = DB::table($tableName)->count();
            
            $category = 'General';
            if (in_array($tableName, $systemTables)) {
                $category = 'System';
            } elseif (str_starts_with($tableName, 'lovewidget_')) {
                $category = 'Love Widget';
            } elseif (str_starts_with($tableName, 'enfoca_')) {
                $category = 'Enfoca';
            }

            if (!isset($categories[$category])) {
                $categories[$category] = 0;
            }
            $categories[$category]++;
            $categories['Todas']++;

            $tableData[] = [
                'name' => $tableName,
                'count' => $count,
                'category' => $category
            ];
        }
        
        // Remove empty categories
        $categories = array_filter($categories, fn($count) => $count > 0);

        return view('hub.db.index', compact('tableData', 'dbName', 'categories'));
    }

    public function show($table)
    {
        $dbName = config('database.connections.mysql.database');
        $tables = array_map(function($t) use ($dbName) {
            $key = "Tables_in_{$dbName}";
            return $t->$key;
        }, DB::select('SHOW TABLES'));

        if (!in_array($table, $tables)) {
            abort(404);
        }

        $columns = Schema::getColumnListing($table);
        $records = DB::table($table)->paginate(50);
        
        $unlockedAt = session('db_unlocked_at');
        $hasAccess = $unlockedAt && now()->timestamp - $unlockedAt < 7200;

        return view('hub.db.show', compact('table', 'columns', 'records', 'hasAccess'));
    }

    public function unlockDb(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (password_verify($request->password, Auth::user()->password)) {
            session(['db_unlocked_at' => now()->timestamp]);
            return back()->with('success', 'Seguridad desbloqueada por 2 horas.');
        }

        return back()->with('error', 'Contraseña incorrecta.');
    }

    public function insertRow(Request $request, $table)
    {
        $unlockedAt = session('db_unlocked_at');
        if (!$unlockedAt || now()->timestamp - $unlockedAt >= 7200) {
            return back()->with('error', 'Acceso denegado. Verifica tu contraseña primero.');
        }

        $dbName = config('database.connections.mysql.database');
        $tables = array_map(function($t) use ($dbName) {
            $key = "Tables_in_{$dbName}";
            return $t->$key;
        }, DB::select('SHOW TABLES'));

        if (!in_array($table, $tables)) {
            abort(404);
        }

        $data = $request->except(['_token']);
        
        $insertData = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        try {
            DB::table($table)->insert($insertData);
            return back()->with('success', 'Registro insertado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al insertar: ' . $e->getMessage());
        }
    }
}
