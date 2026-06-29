<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lovewidget_couple_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('couple_id')->constrained('lovewidget_couples')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('other'); // trip, date, music, other
            $table->string('status')->default('idea'); // idea, planned, completed
            $table->date('target_date')->nullable();
            $table->json('dynamic_data')->nullable(); // Para guardar flights, hotel, packing_list, tickets, etc.
            $table->unsignedBigInteger('linked_album_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lovewidget_couple_plans');
    }
};
