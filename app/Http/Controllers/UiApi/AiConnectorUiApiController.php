<?php

namespace App\Http\Controllers\UiApi;

use App\Data\AiConnectorData;
use App\Data\AiConnectorFormData;
use App\Http\Controllers\Controller;
use App\Models\AiConnector;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AiConnectorUiApiController extends Controller
{
    public function upsertAiConnector(
        AiConnectorFormData $aiConnectorFormData,
        ?AiConnector $aiConnector = null
    ): JsonResponse {
        if ($aiConnector) {
            $aiConnector->update(
                [
                    'name' => '',
                    'llm_options' => $aiConnectorFormData->llmOptions,
                    'ai_api_key_id' => $aiConnectorFormData->aiApiKeyId,
                ]
            );
        } else {
            AiConnector::create(
                [
                    'user_id' => Auth::user()->id,
                    'name' => '',
                    'llm_options' => $aiConnectorFormData->llmOptions,
                    'ai_api_key_id' => $aiConnectorFormData->aiApiKeyId,
                ]
            );
        }

        return new JsonResponse();
    }

    public function deleteAiConnector(AiConnector $aiConnector): JsonResponse
    {
        $aiConnector->delete();

        return new JsonResponse([]);
    }

    public function getAiConnectors(): JsonResponse
    {
        return new JsonResponse([
            'aiConnectors' => AiConnectorData::collect(
                AiConnector::all()
            )
        ]);
    }
}
