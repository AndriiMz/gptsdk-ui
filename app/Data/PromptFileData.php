<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class PromptFileData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly array $content,
        public readonly string $sha
    )
    {

    }
}
