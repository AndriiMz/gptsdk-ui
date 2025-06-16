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
use Gptsdk\Types\Prompt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpClient\HttpClient;

class AiGenerateUiApiController
{
    public function getForm(
        AiApiKeyRepository $aiApiKeyRepository,
        GithubPromptStorage $githubPromptStorage,
    ): JsonResponse {
        //$prompt = $githubPromptStorage->getPrompt('system/generator.prompt');
        $promptArray = $this->getSystemPrompt();
        return new JsonResponse([
            'variables' => $promptArray['variables'],
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


        $promptArray = $this->getSystemPrompt();
        $aiResponses = $completionAi->complete([
            new AiRequest(
                apiKey: $aiApiKey->key,
                aiVendor: $aiApiKey->ai_vendor,
                llmOptions: ['model' => $aiApiKey->default_model],
                compilerType: CompilerType::DOUBLE_BRACKETS,
                prompt: new Prompt(
                    path: '',
                    messages: $promptArray['messages'],
                    variables: $promptArray['variables'],
                    compilerType: CompilerType::DOUBLE_BRACKETS,
                ),
                variableValues: $request->get('values')
            ),
        ]);

        return new JsonResponse([
            'response' => $aiResponses[0]->plainResponse,
        ]);
    }

    private function getSystemPrompt(): array
    {
        return json_decode(
            '{"messages":[{"role":"user","content":"Generate a content for file [[filePath]]. \nIf file ends with .prompt it should be a prompt. \nIf file ends with .md it should be a documentation file.\nHere is a context\n\"\"\"\n[[context]]\n\"\"\"\nHere is an actual file content:\n\"\"\"\n[[content]]\n\"\"\""}],"variables":[{"name":"filePath","value":"","type":"string"},{"name":"context","value":"","type":"text"},{"name":"content","value":"","type":"text"}]}',
            true
        );
    }
}
