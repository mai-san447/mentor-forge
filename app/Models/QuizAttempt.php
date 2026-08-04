<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = ['user_id', 'score', 'total', 'answers'];

    protected function casts(): array
    {
        return ['answers' => 'array'];
    }
}
