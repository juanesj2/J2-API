<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('swipe_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('couple_id')->constrained('couples')->onDelete('cascade');
            $table->foreignId('swipe_question_id')->constrained('swipe_questions')->onDelete('cascade');
            $table->boolean('answer'); // true = swipe right (yes), false = swipe left (no)
            $table->timestamps();

            $table->unique(['user_id', 'swipe_question_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('swipe_answers');
    }
};
