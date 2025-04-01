<?php

namespace App\Http\Controllers\UiApi;

use App\Data\AiLogData;
use App\Data\PromptRequestData;
use App\Logger\DatabaseAiLogger;
use App\Models\AiApiKey;
use App\Models\AiLog;
use App\Models\Repository;
use App\Models\User;
use App\Providers\AiVendorProvider;
use App\Providers\PromptRepositoryProvider;
use Gptsdk\AI\CompletionAi;
use Gptsdk\Compilers\DoubleBracketsPromptCompiler;
use Gptsdk\Enum\CompilerType;
use Gptsdk\Storage\GithubPromptStorage;
use Gptsdk\Types\AiRequest;
use Gptsdk\Types\PromptMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpClient\HttpClient;

class PromptUiApiController
{
    public function deletePrompt(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $paidRepository,
        string $path,
        Request $request
    ): JsonResponse {
        /** @var User $user */
        $user = Auth::user();

        $promptRepositoryProvider
            ->getPromptRepository($paidRepository->type->value)
            ->deletePrompt(
                $paidRepository->token,
                $paidRepository->owner,
                $paidRepository->name,
                $path,
                'Deleted prompt',
                committerName: $user->name,
                committerEmail: $user->email,
                sha: $request->get('sha')
            );

        return new JsonResponse([]);
    }

    public function getPromptResults(
        Repository $paidRepository,
        string $path,
        PromptRequestData $promptRequestData,
        AiVendorProvider $aiVendorProvider
    ): JsonResponse {
        $completionAi = new CompletionAi(
            $aiVendorProvider->getAllAiVendors(),
            [
                CompilerType::DOUBLE_BRACKETS->value => new DoubleBracketsPromptCompiler()
            ],
            new GithubPromptStorage(
                HttpClient::create(),
                $paidRepository->owner,
                $paidRepository->name,
                $paidRepository->token,
            ),
            new DatabaseAiLogger()
        );

        $aiRequests = [];
        $promptMessages = array_map(
            fn(array $prompt) => new PromptMessage(
                role: $prompt['role'],
                content: $prompt['content']
            ),
            $promptRequestData->prompt
        );
        foreach ($promptRequestData->aiConnectors as $aiConnector) {
            foreach ($promptRequestData->variableValues as $variableValues) {
                $aiApiKey = AiApiKey::findOrFail($aiConnector->aiApiKeyId);

                $aiRequests[] = new AiRequest(
                    apiKey: $aiApiKey->key,
                    aiVendor: $aiApiKey->ai_vendor,
                    llmOptions: $aiConnector->llmOptions,
                    compilerType: CompilerType::DOUBLE_BRACKETS,
                    messages: $promptMessages,
                    variableValues: $variableValues,
                    payload: [
                        'repositoryId' => $paidRepository->id,
                        'aiApiKeyId' => $aiConnector->aiApiKeyId,
                        'path' => $path,
                        'aiConnector' => $aiConnector,
                    ]
                );
            }
        }

        $aiLogs = AiLog::whereIn(
            'id',
            array_map(
                fn(AiRequest $aiRequests) => $aiRequests->payload['aiLogId'],
                $completionAi->complete($aiRequests)
            )
        )->get();

        return new JsonResponse([
            'logs' => AiLogData::collect($aiLogs)
        ]);
    }
}
