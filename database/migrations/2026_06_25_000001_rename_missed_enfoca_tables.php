<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renombrar tabla grupo (la migración anterior intentó renombrar 'grupos' en plural)
        if (Schema::hasTable('grupo')) Schema::rename('grupo', 'enfoca_grupos');
        
        // Tablas pivot
        if (Schema::hasTable('desafio_usuario')) Schema::rename('desafio_usuario', 'enfoca_desafio_usuario');
        if (Schema::hasTable('grupo_usuarios')) Schema::rename('grupo_usuarios', 'enfoca_grupo_usuarios');
    }

    public function down(): void
    {
        if (Schema::hasTable('enfoca_grupos')) Schema::rename('enfoca_grupos', 'grupo');
        if (Schema::hasTable('enfoca_desafio_usuario')) Schema::rename('enfoca_desafio_usuario', 'desafio_usuario');
        if (Schema::hasTable('enfoca_grupo_usuarios')) Schema::rename('enfoca_grupo_usuarios', 'grupo_usuarios');
    }
};
