<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = ['category', 'question', 'choices', 'correct_index', 'explanation'];

    protected function casts(): array
    {
        return ['choices' => 'array'];
    }
}
