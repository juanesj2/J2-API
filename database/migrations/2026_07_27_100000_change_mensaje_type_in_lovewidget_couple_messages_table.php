<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // En MySQL, cambiar la columna de texto a LONGTEXT para poder guardar audios en base64 largos
        // Como 'change()' en doctrine/dbal puede tener problemas con longtext en algunas versiones, 
        // usamos SQL directo para mayor compatibilidad si estamos en MySQL.
        
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE lovewidget_couple_messages MODIFY mensaje LONGTEXT');
        } else {
            Schema::table('lovewidget_couple_messages', function (Blueprint $table) {
                $table->longText('mensaje')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE lovewidget_couple_messages MODIFY mensaje TEXT');
        } else {
            Schema::table('lovewidget_couple_messages', function (Blueprint $table) {
                $table->text('mensaje')->nullable()->change();
            });
        }
    }
};
