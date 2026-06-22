<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'question_text'
    ];

    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }
}
