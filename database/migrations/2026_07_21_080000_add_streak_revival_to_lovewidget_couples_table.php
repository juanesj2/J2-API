<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lovewidget_couples', function (Blueprint $table) {
            // Cuando se rompe la racha, guardamos el momento exacto (para las 12h de gracia)
            $table->timestamp('streak_broken_at')->nullable()->after('last_photo_date');
            // Revividores gratis del mes (compartidos por la pareja)
            $table->unsignedInteger('free_revivals')->default(3)->after('streak_broken_at');
            // Revividores de pago (no caducan nunca)
            $table->unsignedInteger('paid_revivals')->default(0)->after('free_revivals');
            // Para saber cuándo resetear los gratis (guardamos año-mes)
            $table->string('free_revivals_reset_month', 7)->nullable()->after('paid_revivals'); // formato: "2026-07"
        });
    }

    public function down(): void
    {
        Schema::table('lovewidget_couples', function (Blueprint $table) {
            $table->dropColumn([
                'streak_broken_at',
                'free_revivals',
                'paid_revivals',
                'free_revivals_reset_month',
            ]);
        });
    }
};
