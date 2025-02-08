<?php

namespace App\Providers;


use App\Interfaces\PromptRepository;

class PromptRepositoryProvider
{
    public function __construct(private readonly array $repositories)
    {}

    public function getPromptRepository(string $type): ?PromptRepository
    {
        return $this->repositories[$type] ?? null;
    }
}
