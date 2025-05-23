<?php

namespace App\Http\Controllers\Ui;

use App\Data\FileData;
use App\Data\FileFormData;
use App\Data\PromptData;
use App\Data\RepositoryData;
use App\Models\Repository;
use App\Models\User;
use App\Providers\PromptRepositoryProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FileUiController
{
    public function edit(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $paidRepository,
        string $path = ''
    ) {
        $promptRepository = $promptRepositoryProvider->getPromptRepository($paidRepository->type->value);

        if (str_ends_with($path, '.prompt')) {
            $prompt = $promptRepository->getFile(
                $paidRepository->token,
                $paidRepository->owner,
                $paidRepository->name,
                $path
            );

            $isEmpty = true;
            $isBroken = false;
            if (null !== $prompt) {
                $isEmpty = !is_array($prompt->content);
                $isBroken = Validator::make(
                    !$isEmpty ? $prompt->content : [],
                    [
                        'messages.*.role' => 'required',
                        'messages.*.content' => 'required',
                        'variables.*.name' => 'required',
                        'variables.*.type' => 'required'
                    ]
                )->fails();

            }

            return Inertia::render('PromptPage', [
                'repository' => RepositoryData::from($paidRepository),
                'path' => $path,
                'file' => $isEmpty || $isBroken ?
                    new FileData(
                        name: $prompt?->name ?? '',
                        sha: $prompt?->sha ?? '',
                        content:  ['messages' => [['role' => '', 'content' => '']], 'variables' => []]
                    ) :
                    $prompt,
                'isBrokenPromptFile' => $isBroken
            ]);
        }

        $file = $promptRepository->getFile(
            $paidRepository->token,
            $paidRepository->owner,
            $paidRepository->name,
            $path
        );

        return Inertia::render('MdPage', [
            'repository' => RepositoryData::from($paidRepository),
            'path' => $path,
            'file' => $file ?? new FileData(
                name: $file?->name ?? '',
                sha: $file?->sha ?? '',
                content: ''
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
            'FilesPage',
            $json
        );
    }

    public function validate(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $paidRepository,
        FileFormData $fileFormData
    ) {
        return redirect()->back();
    }

    public function upsertFile(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $paidRepository,
        FileFormData $fileFormData,
        Request $request,
    ) {
        /** @var User $user */
        $user = Auth::user();
        $promptRepository = $promptRepositoryProvider->getPromptRepository($paidRepository->type->value);

        $promptRepository->savePrompt(
            token: $paidRepository->token,
            owner: $paidRepository->owner,
            repositoryName: $paidRepository->name,
            path: $fileFormData->path,
            message: 'Prompt Update',
            committerName: $user->name,
            committerEmail: $user->email,
            content: $fileFormData->content,
            sha: $fileFormData->sha
        );

        if ($request->headers->get('Accept') === 'application/json') {
            return new JsonResponse([], 201);
        }

        return redirect()->back();
    }
}
