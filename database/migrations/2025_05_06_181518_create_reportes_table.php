<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('foto_id')->constrained('fotografias')->onDelete('cascade');
            $table->text('motivo')->nullable();
            $table->timestamps();

            $table->unique(['usuario_id', 'foto_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('reportes');
    }
};
