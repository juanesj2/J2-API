<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bot_messages', function (Blueprint $table) {
            $table->string('status')->default('sent')->after('is_from_bot'); // pending, sent, failed
        });
    }

    public function down()
    {
        Schema::table('bot_messages', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
