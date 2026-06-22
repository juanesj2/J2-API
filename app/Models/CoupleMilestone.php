<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoupleMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'couple_id',
        'title',
        'date',
        'image_url',
        'story'
    ];

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }
}
