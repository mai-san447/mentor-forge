<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    protected $fillable = [
        'name',
        'role',
        'background',
        'challenge',
        'tone',
        'accent_color',
    ];

    public function scenarios(): HasMany
    {
        return $this->hasMany(Scenario::class);
    }
}
