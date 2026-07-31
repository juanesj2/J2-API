<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('lovewidget_achievements')->updateOrInsert(
            ['id' => 'secret_doom'],
            [
                'title' => 'Slayer',
                'description' => 'Has desatado el modo DOOM en tu mascota.',
                'icon' => 'skull-outline',
                'hints' => json_encode([
                    'Tu mascota parece tener un nombre muy demoníaco...',
                    'El clásico juego de 1993...',
                    'Cambia el nombre de tu mascota a DOOM.'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('lovewidget_achievements')->where('id', 'secret_doom')->delete();
    }
};
