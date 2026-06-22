<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoupleMovie extends Model
{
    protected $fillable = [
        'couple_id', 'title', 'image_url', 'rating', 'who_fell_asleep', 'favorite_quote', 'description', 'genre', 'is_favorite'
    ];}
