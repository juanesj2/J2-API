<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoupleRouletteOption extends Model
{
    use HasFactory;

    protected $table = 'lovewidget_couple_roulette_options';

    protected $fillable = [
        'couple_id',
        'title'
    ];

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }
}
