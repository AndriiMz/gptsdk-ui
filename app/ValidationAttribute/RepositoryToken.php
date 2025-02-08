<?php

namespace App\ValidationAttribute;

use App\Rules\RepositoryTokenRule;
use Attribute;
use Spatie\LaravelData\Attributes\Validation\CustomValidationAttribute;
use Spatie\LaravelData\Support\Validation\ValidationPath;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class RepositoryToken extends CustomValidationAttribute
{
    public function __construct(
        private readonly string $repositoryOwnerFieldName,
        private readonly string $repositoryNameFieldName,
        private readonly string $repositoryTypeFieldName
    )
    { }


    public function getRules(ValidationPath $path): array|object|string
    {
        return [
            new RepositoryTokenRule(
                $this->repositoryTypeFieldName,
                $this->repositoryOwnerFieldName,
                $this->repositoryNameFieldName
            )
        ];
    }
}
