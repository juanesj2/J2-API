<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('swipe_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_text');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('swipe_questions');
    }
};
