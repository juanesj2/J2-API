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
        
        $tableData = [];
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $count = DB::table($tableName)->count();
            $tableData[] = [
                'name' => $tableName,
                'count' => $count
            ];
        }

        return view('hub.db.index', compact('tableData', 'dbName'));
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
        $hasAccess = session('db_unlocked', false);

        return view('hub.db.show', compact('table', 'columns', 'records', 'hasAccess'));
    }

    public function verifyDbPassword(Request $request)
    {
        $request->validate(['password' => 'required']);
        
        if (\Illuminate\Support\Facades\Auth::attempt(['email' => \Illuminate\Support\Facades\Auth::user()->email, 'password' => $request->password])) {
            session(['db_unlocked' => true]);
            return back()->with('success', 'Contraseña verificada. Tienes acceso para insertar datos.');
        }

        return back()->with('error', 'Contraseña incorrecta.');
    }

    public function insert(Request $request, $table)
    {
        if (!session('db_unlocked', false)) {
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
