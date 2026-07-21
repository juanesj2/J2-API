<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couple extends Model
{
    use HasFactory;

    protected $table = 'lovewidget_couples';

    protected $fillable = [
        'user1_id', 'user2_id', 'relationship_start_date',
        'last_poke_at', 'poke_count', 'premium_until',
        'streak_broken_at', 'free_revivals', 'paid_revivals', 'free_revivals_reset_month', 'inventory',
    ];

    protected $casts = [
        'premium_until'    => 'datetime',
        'streak_broken_at' => 'datetime',
        'inventory'        => 'array',
    ];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function photos()
    {
        return $this->hasMany(LovePhoto::class);
    }
}
