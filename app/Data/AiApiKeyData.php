<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class AiApiKeyData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $aiVendor,
        public readonly string $name,
        public readonly \DateTime $createdAt,
        public readonly bool $useForGeneration,
        public readonly ?string $defaultModel = null,
    ) {

    }
}
