<?php
// app/Models/AiApiKey.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $default_model
 */
class AiApiKey extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'key', 'ai_vendor', 'user_id', 'use_for_generation', 'default_model'];

    public function casts()
    {
        return [
            'key' => 'encrypted'
        ];
    }
}
