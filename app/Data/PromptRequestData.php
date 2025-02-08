<?php

namespace App\Data;

use App\PromptRepository\GitHubPromptRepository;
use Gptsdk\Types\PromptMessage;
use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

class PromptRequestData extends Data
{
    public function __construct(
        public readonly array $variableValues,
        /** @var Collection<int, AiConnectorFormData> */
        public readonly Collection $aiConnectors,
        public readonly array $prompt
    ) {

    }
}
