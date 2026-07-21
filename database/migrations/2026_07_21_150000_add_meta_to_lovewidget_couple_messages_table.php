<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lovewidget_couple_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('lovewidget_couple_messages', 'meta')) {
                $table->json('meta')->nullable()->after('reply_to');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lovewidget_couple_messages', function (Blueprint $table) {
            $table->dropColumn('meta');
        });
    }
};
