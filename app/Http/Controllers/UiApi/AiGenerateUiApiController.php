<?php

namespace App\Http\Controllers\UiApi;

use App\Data\AiApiKeyData;
use App\Logger\DatabaseAiLogger;
use App\Providers\AiVendorProvider;
use App\Repository\AiApiKeyRepository;
use Gptsdk\AI\CompletionAi;
use Gptsdk\Compilers\DoubleBracketsPromptCompiler;
use Gptsdk\Enum\CompilerType;
use Gptsdk\Storage\GithubPromptStorage;
use Gptsdk\Types\AiRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpClient\HttpClient;

class AiGenerateUiApiController
{
    public function getForm(
        AiApiKeyRepository $aiApiKeyRepository,
        GithubPromptStorage $githubPromptStorage,
    ): JsonResponse {
        $prompt = $githubPromptStorage->getPrompt('system/generator.prompt');
        //TODO:
        // 1. Show key label +
        // 2. Crete prompt definition
        // 3. Draw prompt form
        // 4. Submit prompt form
        // 5. Show results
        // 6. Add copy button, insert button

        return new JsonResponse([
            'variables' => $prompt->variables,
            'aiApiKey' => AiApiKeyData::optional(
                $aiApiKeyRepository->getAiApiKeyForGeneration()
            ),
        ]);
    }

    public function generate(
        AiApiKeyRepository $aiApiKeyRepository,
        GithubPromptStorage $githubPromptStorage,
        AiVendorProvider $aiVendorProvider,
        Request $request,
    ) {
        $aiApiKey = $aiApiKeyRepository->getAiApiKeyForGeneration();
        $completionAi = new CompletionAi(
            $aiVendorProvider->getAllAiVendors(),
            [CompilerType::DOUBLE_BRACKETS->value => new DoubleBracketsPromptCompiler()],
            $githubPromptStorage
        );

        $aiResponses = $completionAi->complete([
            new AiRequest(
                apiKey: $aiApiKey->key,
                aiVendor: $aiApiKey->ai_vendor,
                llmOptions: ['model' => $aiApiKey->default_model],
                compilerType: CompilerType::DOUBLE_BRACKETS,
                promptPath: 'system/generator.prompt',
                variableValues: $request->get('values')
            )
        ]);

        return new JsonResponse([
            'response' => $aiResponses[0]->plainResponse
        ]);
    }
}
