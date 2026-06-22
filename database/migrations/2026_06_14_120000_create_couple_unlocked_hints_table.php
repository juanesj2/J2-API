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
        Schema::create('couple_unlocked_hints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('couple_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('achievement_id');
            $table->integer('hint_index'); // 0 to 4
            $table->timestamp('unlocked_at')->useCurrent();
            $table->timestamps();

            // A couple can only unlock a specific hint index for an achievement once
            $table->unique(['couple_id', 'achievement_id', 'hint_index'], 'cuh_unique_hint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couple_unlocked_hints');
    }
};
