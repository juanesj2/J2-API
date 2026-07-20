<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LovewidgetGlobalEvent extends Model
{
    use HasFactory;

    protected $table = 'lovewidget_global_events';

    protected $fillable = [
        'title',
        'message',
        'confetti_enabled',
        'confetti_colors',
        'emojis_enabled',
        'emojis_list',
        'top_bar_color',
        'is_active',
        'expires_at'
    ];

    protected $casts = [
        'confetti_enabled' => 'boolean',
        'emojis_enabled' => 'boolean',
        'is_active' => 'boolean',
        'confetti_colors' => 'array',
        'expires_at' => 'datetime',
    ];
}
