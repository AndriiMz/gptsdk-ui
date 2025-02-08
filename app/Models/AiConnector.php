<?php
// app/Models/AiConnector.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array $llm_options
 * @property int $ai_api_key_id
 */
class AiConnector extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'llm_options', 'ai_api_key_id', 'user_id'];

    protected $casts = [
        'llm_options' => 'array',
    ];
}
