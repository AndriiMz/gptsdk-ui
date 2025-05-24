<?php

namespace App\Http\Controllers\UiApi;

use App\Data\AiApiKeyData;
use App\Data\CreateAiApiKeyFormData;
use App\Models\AiApiKey;
use App\Models\AiConnector;
use Illuminate\Http\JsonResponse;

class AiApiKeyUiApiController
{
    public function getKeys(): JsonResponse
    {
        return new JsonResponse([
            'aiApiKeys' =>
                AiApiKeyData::collect(
                    AiApiKey::where('user_id', auth()->id())->get()
                ),
        ]);
    }

    public function deleteKey(AiApiKey $aiApiKey): JsonResponse
    {
        // Update AiConnector to nullify the ai_api_key_id
        AiConnector::where('ai_api_key_id', $aiApiKey->id)->delete();
        $aiApiKey->delete();

        return new JsonResponse([]);
    }
}
