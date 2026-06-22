<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fotografia_id')->constrained('fotografias')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->unique(['usuario_id', 'fotografia_id']);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('likes');
    }
};
