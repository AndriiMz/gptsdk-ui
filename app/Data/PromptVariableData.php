<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Symfony\Contracts\Service\Attribute\Required;

class PromptVariableData extends Data
{
    public function __construct(
        #[Required]
        public readonly string $name,
        #[Required]
        public readonly string $type,
        public readonly ?string $note = null,
    ) {
    }
}
