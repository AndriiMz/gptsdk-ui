<?php
namespace App\Models;

use App\Enum\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'repository_id',
        'ai_api_key_id',
        'llm_options',
        'path',
        'variable_values',
        'input',
        'output',
        'status'
    ];

    protected $casts = [
        'llm_options' => 'array',
        'variable_values' => 'array',
        'input' => 'array',
        'output' => 'array',
        'status' => Status::class
    ];

    public function aiApiKey()
    {
        return $this->belongsTo(AiApiKey::class);
    }


    public function getAiVendorAttribute(): string
    {
        return $this->aiApiKey()->first()?->ai_vendor ?? '';
    }
}
