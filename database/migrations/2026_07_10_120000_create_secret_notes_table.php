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
        Schema::create('secret_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('couple_id');
            $table->unsignedBigInteger('user_id');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('couple_id')->references('id')->on('lovewidget_couples')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Insertar el nuevo logro en la tabla de logros
        DB::table('lovewidget_achievements')->insertOrIgnore([
            'id' => 'secret_love_letter',
            'title' => 'El Buzón Secreto 💌',
            'description' => 'Has descubierto el escondite secreto del amor detrás del contador del tiempo.',
            'icon' => 'mail-unread-outline',
            'hints' => json_encode([
                "Hay lugares en la app donde el tiempo late sin parar...",
                "A veces, los números guardan algo más que matemáticas.",
                "Si nuestro tiempo juntos tuviera un corazón, ¿lo tocarías?",
                "Prueba a darle 3 toques rápidos al corazón del contador de tiempo."
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secret_notes');
        DB::table('lovewidget_achievements')->where('id', 'secret_love_letter')->delete();
    }
};
