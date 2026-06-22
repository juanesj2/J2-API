<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoupleFoodDish extends Model
{
    protected $fillable = [
        'food_place_id', 'name', 'image_url', 'rating', 'description'
    ];

    public function place()
    {
        return $this->belongsTo(CoupleFoodPlace::class, 'food_place_id');
    }}
