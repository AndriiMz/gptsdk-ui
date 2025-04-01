<?php

namespace App\PromptRepository;

use App\Data\BranchData;
use App\Data\PromptFileData;
use App\Data\RepositoryRowData;
use App\Exception\ExpiredApiKeyException;
use App\Interfaces\PromptRepository;
use RecursiveDirectoryIterator as RecursiveDirectoryIteratorAlias;
use RecursiveIteratorIterator;

class TempPromptRepository implements PromptRepository
{
    private const PROMPTS_DIR = 'prompts';

    public function reset()
    {
        $promptsDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), self::PROMPTS_DIR]);

        if (!is_dir($promptsDir)) {
            return;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIteratorAlias($promptsDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        /** @var \SplFileInfo $item */
        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }
    }


    public function isValidRepository(string $token, string $owner, string $repositoryName): bool
    {
        return true;
    }

    public function getBranches(string $token, string $owner, string $repositoryName): array
    {
        if ($token == 'expired') {
            throw new ExpiredApiKeyException();
        }

        return [
            new BranchData(name: 'main', commitSha: 'first')
        ];
    }

    public function getList(string $token, string $owner, string $repositoryName, string $path): array
    {
        $files = array_filter(scandir($this->getPromptsDir()), fn($file) => $file !== '.' && $file !== '..');

        return array_map(
            fn(string $filePath) => new RepositoryRowData(
                type: 'file',
                name: $filePath,
                sha: sha1($filePath),
                path: $filePath,
                isEditable: true
            ), $files
        );
    }

    public function getPrompt(string $token, string $owner, string $repositoryName, string $path): ?PromptFileData
    {
        $content = file_get_contents(
            $this->convertPromptPath($path)
        );

        if (!$content) {
            return null;
        }

        return new PromptFileData(
            name: $path,
            content: json_decode($content, true),
            sha: sha1($path)
        );
    }

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
    ): void {
        file_put_contents($this->convertPromptPath(
            $path
        ), json_encode($content));
    }

    public function deletePrompt(string $token, string $owner, string $repositoryName, string $path, string $message, string $committerName, string $committerEmail, string $sha): void
    {
        $realPath = $this->convertPromptPath($path);
        if (file_exists($realPath)) {
            unlink($realPath);
        }
    }

    private function getPromptsDir(): string
    {
        $promptsDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), self::PROMPTS_DIR]);

        if (!is_dir($promptsDir)) {
            mkdir($promptsDir, 0777, true);
        }

        return $promptsDir;
    }

    private function convertPromptPath(string $promptPath): string
    {
        return implode(
            DIRECTORY_SEPARATOR,
            [
                $this->getPromptsDir(),
                str_replace(
                    DIRECTORY_SEPARATOR,
                    '__',
                    $promptPath,
                )
            ],
        );
    }

}
