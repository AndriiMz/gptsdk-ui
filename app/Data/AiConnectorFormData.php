<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Symfony\Contracts\Service\Attribute\Required;

class AiConnectorFormData extends Data
{
    public function __construct(
        public readonly array $llmOptions,
        #[Required]
        public readonly int $aiApiKeyId
    ) {

    }
}
