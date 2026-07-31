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
        Schema::create('lovewidget_couple_pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('couple_id')->constrained('lovewidget_couples')->onDelete('cascade');
            $table->string('pet_type'); // 'dragon', 'cat', 'dog', etc.
            $table->integer('evolution_phase')->default(1);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            // A couple can only have one of each pet_type
            $table->unique(['couple_id', 'pet_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lovewidget_couple_pets');
    }
};
