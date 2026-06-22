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
        Schema::create('couple_roulette_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('couple_id');
            $table->string('title');
            $table->timestamps();

            $table->foreign('couple_id')->references('id')->on('couples')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couple_roulette_options');
    }
};
