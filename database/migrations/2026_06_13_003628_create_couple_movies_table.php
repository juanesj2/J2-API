<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('couple_movies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('couple_id')->constrained('couples')->onDelete('cascade');
            $table->string('title');
            $table->string('image_url')->nullable();
            $table->integer('rating')->default(5);
            $table->string('who_fell_asleep')->nullable();
            $table->string('favorite_quote')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couple_movies');
    }
};
