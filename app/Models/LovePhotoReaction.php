<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LovePhotoReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'love_photo_id',
        'user_id',
        'content'
    ];

    public function photo()
    {
        return $this->belongsTo(LovePhoto::class, 'love_photo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
