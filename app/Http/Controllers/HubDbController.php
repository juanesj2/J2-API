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

        return view('hub.db.show', compact('table', 'columns', 'records'));
    }
}
