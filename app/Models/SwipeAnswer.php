<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwipeAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'couple_id', 'swipe_question_id', 'answer'];

    public function question()
    {
        return $this->belongsTo(SwipeQuestion::class, 'swipe_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
