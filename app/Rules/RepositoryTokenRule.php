<?php

namespace App\Rules;

use App\Providers\PromptRepositoryProvider;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\App;

class RepositoryTokenRule implements ValidationRule, DataAwareRule
{
    private array $data = [];

    public function __construct(
        private readonly string $repositoryTypeFieldName,
        private readonly string $repositoryOwnerFieldName,
        private readonly string $repositoryNameFieldName
    )
    { }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var PromptRepositoryProvider $promptRepositoryProvider */
        $promptRepositoryProvider = App::make(PromptRepositoryProvider::class);
        $promptRepository = $promptRepositoryProvider->getPromptRepository(
            $this->data[$this->repositoryTypeFieldName]
        );

        $isValid = $promptRepository->isValidRepository(
            $value,
            $this->data[$this->repositoryOwnerFieldName],
            $this->data[$this->repositoryNameFieldName]
        );

        if (!$isValid) { //TODO: use validation result to give more info is token invalid or route doesn't exists
            $fail('Invalid GitHub token.');
        }
    }
}
