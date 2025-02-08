<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class BranchData extends Data
{
    public function __construct(
        public readonly string $name
    )
    { }
}
