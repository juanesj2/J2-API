<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couple extends Model
{
    use HasFactory;

    protected $fillable = ['user1_id', 'user2_id', 'relationship_start_date', 'last_poke_at', 'poke_count'];

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
