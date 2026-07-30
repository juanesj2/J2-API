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
        Schema::table('lovewidget_couples', function (Blueprint $table) {
            $table->string('pet_name', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lovewidget_couples', function (Blueprint $table) {
            $table->dropColumn('pet_name');
        });
    }
};
