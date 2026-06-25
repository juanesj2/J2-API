<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

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

        $unlockedAt = session('db_unlocked_at');
        $hasAccess = $unlockedAt && now()->timestamp - $unlockedAt < 7200;

        return view('hub.db.index', compact('tableData', 'dbName', 'categories', 'unlockedAt', 'hasAccess'));
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

        return view('hub.db.show', compact('table', 'columns', 'records', 'hasAccess', 'unlockedAt'));
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

        $data = $request->except(['_token']);
        
        $insertData = [];
        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                $insertData[$key] = $value;
            }
        }

        $columns = Schema::getColumnListing($table);
        $columnsLower = array_map('strtolower', $columns);
        if (in_array('created_at', $columnsLower) && !isset($insertData['created_at'])) {
            $insertData['created_at'] = now();
        }
        if (in_array('updated_at', $columnsLower) && !isset($insertData['updated_at'])) {
            $insertData['updated_at'] = now();
        }

        try {
            DB::table($table)->insert($insertData);
            return back()->with('success', 'Registro insertado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al insertar: ' . $e->getMessage());
        }
    }

    public function updateRow(Request $request, $table, $id)
    {
        $unlockedAt = session('db_unlocked_at');
        if (!$unlockedAt || now()->timestamp - $unlockedAt >= 7200) {
            return back()->with('error', 'Acceso denegado. Verifica tu contraseña primero.');
        }

        $data = $request->except(['_token', '_method']);
        
        $updateData = [];
        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                $updateData[$key] = $value;
            }
        }

        $columns = Schema::getColumnListing($table);
        $columnsLower = array_map('strtolower', $columns);
        if (in_array('updated_at', $columnsLower) && !isset($updateData['updated_at'])) {
            $updateData['updated_at'] = now();
        }

        try {
            DB::table($table)->where('id', $id)->update($updateData);
            return back()->with('success', 'Registro actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function deleteRow(Request $request, $table, $id)
    {
        $unlockedAt = session('db_unlocked_at');
        if (!$unlockedAt || now()->timestamp - $unlockedAt >= 7200) {
            return back()->with('error', 'Acceso denegado. Verifica tu contraseña primero.');
        }

        try {
            DB::table($table)->where('id', $id)->delete();
            return back()->with('success', 'Registro eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    public function extendSession(Request $request)
    {
        $unlockedAt = session('db_unlocked_at');
        if ($unlockedAt && (now()->timestamp - $unlockedAt) < 7200) {
            session(['db_unlocked_at' => now()->timestamp]);
            return redirect()->back()->with('success', 'Sesión de la base de datos extendida por 2 horas más.');
        }

        return redirect()->route('hub.db.index')->with('error', 'La sesión ha expirado o no está desbloqueada.');
    }

    public function executeSql(Request $request)
    {
        $unlockedAt = session('db_unlocked_at');
        if (!$unlockedAt || now()->timestamp - $unlockedAt >= 7200) {
            return back()->with('error', 'Acceso denegado. Verifica tu contraseña primero.');
        }

        $request->validate([
            'query' => 'required|string'
        ]);

        $sql = trim($request->input('query'));
        
        if (empty($sql)) {
            return back()->with('error', 'La consulta SQL está vacía.');
        }

        try {
            // Check if it's a SELECT query
            if (stripos($sql, 'select') === 0 || stripos($sql, 'show') === 0 || stripos($sql, 'describe') === 0 || stripos($sql, 'explain') === 0) {
                $results = DB::select($sql);
                // Return success with count
                $count = count($results);
                return back()->with('success', "Consulta ejecutada correctamente. {$count} filas devueltas.");
            } else {
                // Non-select queries (INSERT, UPDATE, DELETE, DROP, etc)
                $affected = DB::statement($sql);
                return back()->with('success', 'Sentencia SQL ejecutada correctamente.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error SQL: ' . $e->getMessage());
        }
    }
}
