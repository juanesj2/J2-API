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
        Schema::table('couple_movies', function (Blueprint $table) {
            $table->string('genre')->nullable();
            $table->boolean('is_favorite')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('couple_movies', function (Blueprint $table) {
            $table->dropColumn(['genre', 'is_favorite']);
        });
    }
};
