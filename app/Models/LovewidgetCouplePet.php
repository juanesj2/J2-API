<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LovewidgetCouplePet extends Model
{
    protected $table = 'lovewidget_couple_pets';

    protected $fillable = [
        'couple_id',
        'pet_type',
        'evolution_phase',
        'is_active',
    ];

    protected $casts = [
        'evolution_phase' => 'integer',
        'is_active' => 'boolean',
    ];

    public function couple(): BelongsTo
    {
        return $this->belongsTo(LovewidgetCouple::class, 'couple_id');
    }
}
