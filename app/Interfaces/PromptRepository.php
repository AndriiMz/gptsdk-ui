<?php

namespace App\Interfaces;

use App\Data\MdFileData;
use App\Data\FileData;
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

    public function getFile(
        string $token,
        string $owner,
        string $repositoryName,
        string $path
    ): null|FileData;

    public function savePrompt(
        string $token,
        string $owner,
        string $repositoryName,
        string $path,
        string $message,
        string $committerName,
        string $committerEmail,
        string $content,
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
