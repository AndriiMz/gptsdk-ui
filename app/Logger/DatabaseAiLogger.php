<?php

namespace App\Logger;

use App\Enum\Status;
use App\Models\AiLog;
use Gptsdk\Interfaces\AILogger;
use Gptsdk\Types\AiRequest;
use Illuminate\Support\Facades\Auth;

class DatabaseAiLogger implements AILogger
{

    public function log(AiRequest $aiRequest): AiRequest
    {
        $aiLog = AiLog::create([
            'user_id' => Auth::user()->id,
            'repository_id' => $aiRequest->payload['repositoryId'],
            'ai_api_key_id' => $aiRequest->payload['aiApiKeyId'],
            'path' => $aiRequest->payload['path'],
            'llm_options' => $aiRequest->llmOptions,
            'variable_values' => $aiRequest->variableValues,
            'input' => $aiRequest->compiledMessages,
            'output' => $aiRequest->plainResponse,
            'status' => match($aiRequest->responseStatus->value) {
                \Gptsdk\Enum\Status::SUCCESS->value => Status::SUCCESS->value,
                default => Status::ERROR->value,
            }
        ]);

        $aiRequest->payload['aiLogId'] = $aiLog->id;

        return $aiRequest;
    }
}
