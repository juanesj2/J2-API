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
        // 1. Add user1_poke_count and user2_poke_count to couples table
        Schema::table('lovewidget_couples', function (Blueprint $table) {
            if (!Schema::hasColumn('lovewidget_couples', 'user1_poke_count')) {
                $table->integer('user1_poke_count')->default(0)->after('poke_count');
            }
            if (!Schema::hasColumn('lovewidget_couples', 'user2_poke_count')) {
                $table->integer('user2_poke_count')->default(0)->after('user1_poke_count');
            }
        });

        // 2. Insert the achievement for discovering secret stats
        DB::table('lovewidget_achievements')->updateOrInsert(
            ['id' => 'secret_stats_unlocked'],
            [
                'title' => 'Caja Fuerte Abierta',
                'description' => 'Has descubierto las Estadísticas Secretas con el código correcto.',
                'icon' => 'pie-chart',
                'hints' => json_encode([
                    'Hay algo oculto en el Panel de Control...',
                    'Prueba a tocar las cosas importantes de arriba.',
                    '¿Qué pasa si te tocas a ti mismo?',
                    'Tú, luego yo...',
                    'Tú, luego yo, y después presiona nuestro amor por un momento.'
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
        Schema::table('lovewidget_couples', function (Blueprint $table) {
            $table->dropColumn(['user1_poke_count', 'user2_poke_count']);
        });

        DB::table('lovewidget_achievements')->where('id', 'secret_stats_unlocked')->delete();
    }
};
