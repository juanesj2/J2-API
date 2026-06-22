<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('love_albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('couple_id')->constrained('couples')->onDelete('cascade');
            $table->string('name');
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });

        Schema::table('love_photos', function (Blueprint $table) {
            $table->foreignId('album_id')->nullable()->constrained('love_albums')->onDelete('set null')->after('couple_id');
        });
    }

    public function down(): void
    {
        Schema::table('love_photos', function (Blueprint $table) {
            $table->dropForeign(['album_id']);
            $table->dropColumn('album_id');
        });

        Schema::dropIfExists('love_albums');
    }
};
