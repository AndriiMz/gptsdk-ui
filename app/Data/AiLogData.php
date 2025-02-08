<?php

namespace App\Data;

use App\Enum\Status;
use Spatie\LaravelData\Data;

class AiLogData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly array $variableValues,
        public readonly array $llmOptions,
        public readonly array $input,
        public readonly array $output,
        public readonly \DateTime $createdAt,
        public readonly Status $status
    ) {

    }
}
