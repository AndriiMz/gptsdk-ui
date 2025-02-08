<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class PromptFormData extends Data
{
    private const PROMPT_NAME_REGEX = '/^\/?[^\/\0]+(\/[^\/\0]+)*\.prompt$/';

    public function __construct(
        public readonly PromptData $content,
        #[Required]
        #[Regex(self::PROMPT_NAME_REGEX)]
        public readonly string $path,
        public readonly ?string $sha = null,
    )
    {}
}
