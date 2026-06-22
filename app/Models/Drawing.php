<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drawing extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'couple_id', 'drawing_prompt_id', 'image_path'];

    public function prompt()
    {
        return $this->belongsTo(DrawingPrompt::class, 'drawing_prompt_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
