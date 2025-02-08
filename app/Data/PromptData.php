<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class PromptData extends Data
{
    public function __construct(
        /** @var Collection<int, PromptMessageData> */
        public readonly Collection $messages,
        /** @var Collection<int, PromptVariableData> */
        public readonly Collection $variables,
    )
    {}
}
