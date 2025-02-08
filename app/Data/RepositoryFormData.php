<?php

namespace App\Data;

use App\PromptRepository\GitHubPromptRepository;
use App\ValidationAttribute\RepositoryToken;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class RepositoryFormData extends Data
{
    public function __construct(
        public readonly string $type,
        #[Required]
        #[RepositoryToken(
            repositoryOwnerFieldName: 'owner',
            repositoryNameFieldName: 'name',
            repositoryTypeFieldName: 'type'
        )]
        public readonly string $token,
        #[Required]
        #[Regex(pattern: GitHubPromptRepository::REPOSITORY_URL_PATTERN)]
        public readonly string $url,
        public readonly string $name,
        public readonly string $owner,
        public readonly ?int $id = null
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        preg_match(GitHubPromptRepository::REPOSITORY_URL_PATTERN, $properties['url'], $matches);

        $properties['owner'] = $matches['owner'] ?? '';
        $properties['name'] =  preg_replace('/\.git$/i', '', $matches['repo'] ?? '');

        return $properties;
    }

}
