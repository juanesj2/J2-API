<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoveAlbum extends Model
{
    use HasFactory;

    protected $fillable = [
        'couple_id',
        'name',
        'cover_image'
    ];

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }

    public function photos()
    {
        return $this->hasMany(LovePhoto::class, 'album_id');
    }
}
