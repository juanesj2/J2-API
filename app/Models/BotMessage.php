<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_source',
        'phone_number',
        'contact_name',
        'body',
        'is_from_bot',
    ];
}
