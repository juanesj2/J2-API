<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('couples', function (Blueprint $table) {
            $table->dateTime('relationship_start_date')->nullable();
            $table->dateTime('last_poke_at')->nullable();
            $table->integer('poke_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('couples', function (Blueprint $table) {
            $table->dropColumn(['relationship_start_date', 'last_poke_at', 'poke_count']);
        });
    }
};
