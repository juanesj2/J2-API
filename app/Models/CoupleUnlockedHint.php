<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoupleUnlockedHint extends Model
{
    use HasFactory;

    protected $fillable = [
        'couple_id',
        'user_id',
        'achievement_id',
        'hint_index',
        'unlocked_at',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'hint_index' => 'integer',
    ];

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
