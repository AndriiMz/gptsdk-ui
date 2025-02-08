<?php

namespace App\Data;

use App\Enum\SubscriptionStatus;
use Spatie\LaravelData\Data;

class RepositoryData extends Data
{
    public function __construct(
       // public readonly string $token,
        public readonly string $url,
        public readonly string $name,
        public readonly string $owner,
        public readonly SubscriptionStatus $subscriptionStatus,
        public readonly ?int $id
    )
    {}
}
