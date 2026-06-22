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
        Schema::table('couple_food_places', function (Blueprint $table) {
            $table->boolean('is_favorite')->default(false)->after('description');
            $table->string('category')->nullable()->after('is_favorite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('couple_food_places', function (Blueprint $table) {
            $table->dropColumn(['is_favorite', 'category']);
        });
    }
};
