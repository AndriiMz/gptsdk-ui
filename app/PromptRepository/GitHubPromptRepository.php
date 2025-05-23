<?php

namespace App\PromptRepository;

use App\Data\BranchData;
use App\Data\FileData;
use App\Data\RepositoryRowData;
use App\Exception\ExpiredApiKeyException;
use App\Interfaces\PromptRepository;
use App\Util\TypeUtil;
use Nette\Utils\Type;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GitHubPromptRepository implements PromptRepository
{

    private array $files = [];
    private readonly HttpClientInterface $httpClient;
    public function __construct()
    {
        $this->httpClient = HttpClient::createForBaseUri('https://api.github.com');
    }
    public function getBranches(
        string $token,
        string $owner,
        string $repositoryName
    ): array {
        $branches = $this->httpClient->request(
            'GET',
            "repos/$owner/$repositoryName/branches",
            [
                'headers' => [
                    'X-GitHub-Api-Version' => '2022-11-28',
                    'Accept' => 'application/vnd.github+json',
                    'Authorization' => "Bearer $token"
                ]
            ]
        )->toArray(false);

        if (isset($branches['status']) && $branches['status'] !== 200) {
            throw new ExpiredApiKeyException();
        }

        return BranchData::collect(
            array_map(
                fn(array $branch) => [
                    'name' => $branch['name'],
                    'commitSha' => $branch['commit']['sha']
                ],
                $branches
            )
        );
    }

    public function getList(
        string $token,
        string $owner,
        string $repositoryName,
        string $path
    ): array {
        return $this->getContents(
            $token,
            $owner,
            $repositoryName,
            $path
        ) ?? [];
    }

    public function getFile(
        string $token,
        string $owner,
        string $repositoryName,
        string $path
    ): null|FileData {
        if (isset($this->files[$path])) {
            return $this->files[$path];
        }

        $file = $this->getContents(
            $token,
            $owner,
            $repositoryName,
            $path
        );

        $this->files[$path] = $file;

        return $file;
    }

    public function getContents(
        string $token,
        string $owner,
        string $repositoryName,
        string $path
    ): array|FileData|null {
        $response = $this->httpClient->request(
            'GET',
            "repos/$owner/$repositoryName/contents/$path",
            [
                'headers' => [
                    'X-GitHub-Api-Version' => '2022-11-28',
                    'Accept' => 'application/vnd.github+json',
                    'Authorization' => "Bearer $token"
                ]
            ]
        )->toArray(false);

        if (isset($response['status']) && $response['status'] == 404) {
            return null;
        }

        if (isset($response['status']) && $response['status'] != 200) {
            throw new ExpiredApiKeyException();
        }

        if (isset($response['content'])) {
            $encodedContent = base64_decode(
                $response['content']
            );

            return new FileData(
                name: $response['name'],
                content: TypeUtil::isJson($encodedContent) ?
                    json_decode($encodedContent, true) :
                    $encodedContent,
                sha: $response['sha']
            );
        }

        return RepositoryRowData::collect($response);
    }

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
    ): void {
        $response = $this->httpClient->request(
            'PUT',
            "repos/$owner/$repositoryName/contents/$path",
            [
                'headers' => [
                    'X-GitHub-Api-Version' => '2022-11-28',
                    'Accept' => 'application/vnd.github+json',
                    'Authorization' => "Bearer $token"
                ],
                'json' => [
                    'message' => $message,
                    'committer' => [
                        'name' => strlen($committerName) > 0 ? $committerName : 'Admin',
                        'email' => $committerEmail
                    ],
                    'content' => base64_encode($content),
                    'sha' => $sha
                ]
            ]
        )->toArray(false);
    }

    public function deletePrompt(
        string $token,
        string $owner,
        string $repositoryName,
        string $path,
        string $message,
        string $committerName,
        string $committerEmail,
        string $sha
    ): void {
        $this->httpClient->request(
            'DELETE',
            "repos/$owner/$repositoryName/contents/$path",
            [
                'headers' => [
                    'X-GitHub-Api-Version' => '2022-11-28',
                    'Accept' => 'application/vnd.github+json',
                    'Authorization' => "Bearer $token"
                ],
                'json' => [
                    'message' => $message,
                    'committer' => [
                        'name' => strlen($committerName) > 0 ? $committerName : 'Admin',
                        'email' => $committerEmail
                    ],
                    'sha' => $sha
                ]
            ]
        )->toArray(false);
    }

    public function isValidRepository(string $token, string $owner, string $repositoryName): bool
    {
        return $this->httpClient->request(
            'GET',
            "repos/$owner/$repositoryName/contents",
            [
                'headers' => [
                    'X-GitHub-Api-Version' => '2022-11-28',
                    'Accept' => 'application/vnd.github+json',
                    'Authorization' => "Bearer $token"
                ]
            ]
        )->getStatusCode() !== 401;
    }
}
