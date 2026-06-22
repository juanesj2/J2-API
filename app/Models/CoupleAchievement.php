<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoupleAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'couple_id',
        'achievement_id',
        'unlocked_at',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
    ];

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }
}
