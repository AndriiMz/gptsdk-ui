<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class PromptMessageData extends Data
{
    public function __construct(
        #[Required]
        public readonly string $role,
        #[Required]
        public readonly string $content,
    )
    {
    }
}
