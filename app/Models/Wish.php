<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wish extends Model
{
    use HasFactory;

    protected $table = 'lovewidget_wishes';

    protected $fillable = ['couple_id', 'title', 'completed'];

    protected $casts = [
        'completed' => 'boolean',
    ];
}
