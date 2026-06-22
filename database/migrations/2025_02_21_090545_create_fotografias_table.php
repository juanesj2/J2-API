<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fotografias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->text('direccion_imagen');
            $table->text('direccion_optimizada')->nullable();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->boolean('vetada')->default(false);

            // Metadatos de la fotografía
            $table->unsignedInteger('ISO')->nullable();
            $table->string('velocidad_obturacion', 20)->nullable();
            $table->decimal('apertura', 4, 1)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('fotografias');
    }
};
