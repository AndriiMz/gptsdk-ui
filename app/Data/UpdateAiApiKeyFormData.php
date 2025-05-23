<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class UpdateAiApiKeyFormData extends Data
{
    public function __construct(
        #[Min(1)]
        public readonly ?string $name,
        public readonly ?bool $useForGeneration,
        public readonly ?string $defaultModel,
    ) {

    }
}
