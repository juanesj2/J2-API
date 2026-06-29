<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouplePlan extends Model
{
    use HasFactory;

    protected $table = 'lovewidget_couple_plans';

    protected $fillable = [
        'couple_id',
        'title',
        'description',
        'category',
        'status',
        'target_date',
        'dynamic_data',
        'linked_album_id'
    ];

    protected $casts = [
        'dynamic_data' => 'array',
        'target_date' => 'date'
    ];

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }
}
