<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CoupleFoodDish extends Model
{
    use HasFactory;

    protected $table = 'lovewidget_couple_food_dishes';

    protected $fillable = [
        'food_place_id', 'name', 'image_url', 'rating', 'description'
    ];

    public function place()
    {
        return $this->belongsTo(CoupleFoodPlace::class, 'food_place_id');
    }}
