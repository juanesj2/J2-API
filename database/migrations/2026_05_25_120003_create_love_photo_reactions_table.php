<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('love_photo_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('love_photo_id')->constrained('love_photos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('content'); // e.g. "❤️", "😂"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('love_photo_reactions');
    }
};
