<?php

namespace App\Interfaces;

use App\Data\PromptFileData;
use App\Data\RepositoryRowData;

interface PromptRepository
{
    // TODO: should depend on provider
    public const REPOSITORY_URL_PATTERN = '/(?:https?:\/\/|git@)github\.com[:\/](?<owner>[^\/]+)\/(?<repo>[^\/]+)(?:\.git)?/i';

    public function isValidRepository(
        string $token,
        string $owner,
        string $repositoryName
    ): bool;

    public function getBranches(
        string $token,
        string $owner,
        string $repositoryName
    ): array;

    /**
     * @return RepositoryRowData[]
     */
    public function getList(
        string $token,
        string $owner,
        string $repositoryName,
        string $path
    ): array;

    public function getPrompt(
        string $token,
        string $owner,
        string $repositoryName,
        string $path
    ): ?PromptFileData;

    public function savePrompt(
        string $token,
        string $owner,
        string $repositoryName,
        string $path,
        string $message,
        string $committerName,
        string $committerEmail,
        array $content,
        ?string $sha = null
    ): void;

    public function deletePrompt(
        string $token,
        string $owner,
        string $repositoryName,
        string $path,
        string $message,
        string $committerName,
        string $committerEmail,
        string $sha
    ): void;
}
