<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class FileData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly array|string $content,
        public readonly string $sha
    )
    {

    }
}
