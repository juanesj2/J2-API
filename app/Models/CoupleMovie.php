<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoupleMovie extends Model
{
    use HasFactory;

    protected $table = 'lovewidget_couple_movies';

    protected $fillable = [
        'couple_id', 'title', 'image_url', 'rating', 'who_fell_asleep', 'favorite_quote', 'description', 'genre', 'is_favorite'
    ];}
