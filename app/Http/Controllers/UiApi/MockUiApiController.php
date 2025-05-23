<?php

namespace App\Http\Controllers\UiApi;

use App\Data\MockData;
use App\Models\Repository;
use App\Models\User;
use App\Providers\PromptRepositoryProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class MockUiApiController
{
    public function getMocks(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $paidRepository,
        string $path
    ) {
        $promptRepository = $promptRepositoryProvider->getPromptRepository($paidRepository->type->value);
        $prompt = $promptRepository->getFile(
            token: $paidRepository->token,
            owner: $paidRepository->owner,
            repositoryName: $paidRepository->name,
            path: $path
        );

        return new JsonResponse([
            'mocks' => MockData::collect($prompt->content['mocks'] ?? []),
        ]);
    }

    public function deleteMock(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $paidRepository,
        string $path,
        Request $request
    ) {
        /** @var User $user */
        $user = Auth::user();
        $mockHash = $request->get('hash');
        $promptRepository = $promptRepositoryProvider->getPromptRepository($paidRepository->type->value);
        $prompt = $promptRepository->getFile(
            token: $paidRepository->token,
            owner: $paidRepository->owner,
            repositoryName: $paidRepository->name,
            path: $path
        );

        $updatedContent = $prompt->content;
        unset($updatedContent['mocks'][$mockHash]);

        $promptRepository->savePrompt(
            token: $paidRepository->token,
            owner: $paidRepository->owner,
            repositoryName: $paidRepository->name,
            path: $path,
            message: 'Mock Added',
            committerName: $user->name,
            committerEmail: $user->email,
            content: json_encode($updatedContent),
            sha: $prompt->sha
        );

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }

    public function upsertMock(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $paidRepository,
        MockData $promptFormData,
        string $path,
    ) {
        /** @var User $user */
        $user = Auth::user();
        $promptRepository = $promptRepositoryProvider->getPromptRepository($paidRepository->type->value);

        $prompt = $promptRepository->getFile(
            token: $paidRepository->token,
            owner: $paidRepository->owner,
            repositoryName: $paidRepository->name,
            path: $path
        );

        $updatedContent = $prompt->content;

        if (!isset($updatedContent['mocks'])) {
            $updatedContent['mocks'] = [];
        }

        $updatedContent['mocks'][sha1(json_encode($promptFormData->variableValues))] = [
            'variableValues' => $promptFormData->variableValues,
            'output'         => $promptFormData->output
        ];

        $promptRepository->savePrompt(
            token: $paidRepository->token,
            owner: $paidRepository->owner,
            repositoryName: $paidRepository->name,
            path: $path,
            message: 'Mock Added',
            committerName: $user->name,
            committerEmail: $user->email,
            content: json_encode($updatedContent),
            sha: $prompt->sha
        );

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
