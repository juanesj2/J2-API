<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecretNote extends Model
{
    use HasFactory;

    protected $table = 'secret_notes';

    protected $fillable = [
        'couple_id',
        'user_id',
        'content',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }
}
