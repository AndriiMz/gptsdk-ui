<?php

namespace App\Http\Controllers\Ui;

use App\Data\PromptFileData;
use App\Data\PromptFormData;
use App\Data\RepositoryData;
use App\Models\Repository;
use App\Models\User;
use App\Providers\PromptRepositoryProvider;
use Gptsdk\Types\PromptMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromptUiController
{
    public function edit(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $paidRepository,
        string $path = ''
    ) {
        $promptRepository = $promptRepositoryProvider->getPromptRepository($paidRepository->type->value);
        $emptyPrompt = ['messages' => [['role' => '', 'content' => '']], 'variables' => []];

        if (str_ends_with($path, '.prompt')) {
            $prompt = $promptRepository->getPrompt(
                $paidRepository->token,
                $paidRepository->owner,
                $paidRepository->name,
                $path
            );

            if (null === $prompt) {
                throw new NotFoundHttpException();
            }

            $validator = Validator::make(
                $prompt->content,
                [
                    'messages.*.role' => 'required',
                    'messages.*.content' => 'required',
                    'variables.*.name' => 'required',
                    'variables.*.type' => 'required'
                ]
            );

            return Inertia::render('PromptPage', [
                'repository' => RepositoryData::from($paidRepository),
                'path' => $path,
                'prompt' => $validator->fails() ?
                    new PromptFileData(
                        name: $prompt->name,
                        sha: $prompt->sha,
                        content: $emptyPrompt
                    ) :
                    $prompt,
                'isBrokenPromptFile' => $validator->fails()
            ]);
        }

        return Inertia::render('PromptPage', [
            'repository' => RepositoryData::from($paidRepository),
            'path' => $path,
            'prompt' => new PromptFileData(
                name: '',
                content: $emptyPrompt,
                sha: ''
            )
        ]);
    }

    public function list(
        Request $request,
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $repository,
        string $path = ''
    ) {
        $promptRepository = $promptRepositoryProvider->getPromptRepository($repository->type->value);

        $json = [
            'repository' => RepositoryData::from($repository),
            'path' => $path,
            'branches' => $promptRepository->getBranches(
                $repository->token,
                $repository->owner,
                $repository->name
            ),
            'files' => $promptRepository->getList(
                $repository->token,
                $repository->owner,
                $repository->name,
                $path
            )
        ];

        if ($request->headers->get('Accept') === 'application/json') {
            return new JsonResponse($json);
        }

        return Inertia::render(
            'PromptsPage',
            $json
        );
    }

    public function validate(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $paidRepository,
        PromptFormData $promptFormData
    ) {
        return redirect()->back();
    }

    public function upsertPrompt(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $paidRepository,
        PromptFormData $promptFormData
    ) {
        /** @var User $user */
        $user = Auth::user();
        $promptRepository = $promptRepositoryProvider->getPromptRepository($paidRepository->type->value);

        $promptRepository->savePrompt(
            token: $paidRepository->token,
            owner: $paidRepository->owner,
            repositoryName: $paidRepository->name,
            path: $promptFormData->path,
            message: 'Prompt Update',
            committerName: $user->name,
            committerEmail: $user->email,
            content: $promptFormData->content->toArray(),
            sha: $promptFormData->sha
        );

        return redirect()->back();
    }
}
