<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrawingPrompt extends Model
{
    use HasFactory;

    protected $table = 'lovewidget_drawing_prompts';

    protected $fillable = ['prompt_text'];

    public function drawings()
    {
        return $this->hasMany(Drawing::class);
    }
}
