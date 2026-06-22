<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('drawing_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('prompt_text');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drawing_prompts');
    }
};
