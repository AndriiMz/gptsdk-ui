<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class VariableValueData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly array $variableValues,
    )
    { }
}
