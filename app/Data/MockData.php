<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class MockData extends Data
{
    #[Computed]
    public string $hash;

    public function __construct(
        public readonly array $variableValues,
        public readonly array $output
    )
    {
        $this->hash = sha1(json_encode([$variableValues, $output]));
    }
}
