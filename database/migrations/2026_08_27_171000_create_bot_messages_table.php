<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_messages', function (Blueprint $table) {
            $table->id();
            $table->string('app_source')->default('whatsapp'); // love-widget, enfoca, whatsapp
            $table->string('phone_number'); // ID del chat
            $table->string('contact_name')->nullable();
            $table->text('body');
            $table->boolean('is_from_bot')->default(false); // true si lo envía el bot, false si lo recibe
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_messages');
    }
};
