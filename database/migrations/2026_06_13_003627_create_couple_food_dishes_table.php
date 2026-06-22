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
        Schema::create('couple_food_dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_place_id')->constrained('couple_food_places')->onDelete('cascade');
            $table->string('name');
            $table->string('image_url')->nullable();
            $table->integer('rating')->default(5);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couple_food_dishes');
    }
};
