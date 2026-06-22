<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LovePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'couple_id',
        'user_id',
        'image_path',
        'description',
        'fecha_recuerdo'
    ];

    public function album()
    {
        return $this->belongsTo(LoveAlbum::class);
    }

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(LovePhotoReaction::class);
    }
}
