<?php
// app/Models/AiApiKey.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiApiKey extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'key', 'ai_vendor', 'user_id'];

    public function casts()
    {
        return [
            'key' => 'encrypted'
        ];
    }
}
