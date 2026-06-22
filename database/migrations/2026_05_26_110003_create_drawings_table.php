<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('drawings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('couple_id')->constrained('couples')->onDelete('cascade');
            $table->foreignId('drawing_prompt_id')->constrained('drawing_prompts')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();

            $table->unique(['user_id', 'drawing_prompt_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('drawings');
    }
};
