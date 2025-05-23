<?php

namespace App\Data;

use App\PromptRepository\GitHubPromptRepository;
use Spatie\LaravelData\Data;

class RepositoryRowData extends Data
{
    public function __construct(
        public readonly string $type,
        public readonly string $name,
        public readonly ?string $sha,
        public readonly ?string $path,
        public readonly bool $isEditable
    )
    {

    }

    public static function prepareForPipeline(array $properties): array
    {
        $properties['isEditable'] = in_array(
            pathinfo($properties['name'], PATHINFO_EXTENSION),
            ['prompt', 'md']
        );

        return $properties;
    }
}
