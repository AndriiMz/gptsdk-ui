<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiVariableValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'repository_id',
        'path',
        'variable_values',
    ];

    protected $casts = [
        'variable_values' => 'array'
    ];
}
