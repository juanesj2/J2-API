<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('couple_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('couple_id')->constrained('couples')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('love_photo_id')->nullable()->constrained('love_photos')->onDelete('set null'); // En caso de que sea respuesta a una foto
            $table->text('mensaje');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couple_messages');
    }
};
