<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupo_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupo')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('rol')->default('miembro');
            $table->timestamps();

            $table->unique(['grupo_id', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_usuarios');
    }
};
