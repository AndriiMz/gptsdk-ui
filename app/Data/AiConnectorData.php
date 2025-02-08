<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class AiConnectorData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly array $llmOptions,
        public readonly string $aiApiKeyId
    ) {
    }
}
