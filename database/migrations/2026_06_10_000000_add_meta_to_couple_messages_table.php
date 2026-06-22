<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('couple_messages', function (Blueprint $table) {
            $table->json('reply_to')->nullable();
            $table->json('reactions')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('couple_messages', function (Blueprint $table) {
            $table->dropColumn(['reply_to', 'reactions']);
        });
    }
};
