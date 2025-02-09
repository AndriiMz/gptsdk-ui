<?php

namespace App\Models;

use App\Enum\PromptRepositoryType;
use App\Enum\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $token
 * @property string $owner
 * @property string $name
 * @property string $url
 * @property PromptRepositoryType $type
 */
class Repository extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'owner', 'name', 'url', 'user_id', 'subscription_status', 'type'];

    protected $casts = [
        'subscription_status' => SubscriptionStatus::class,
        'type' => PromptRepositoryType::class
    ];
}
