<?php

namespace App\Http\Controllers\Ui;

use App\Data\AiApiKeyData;
use App\Data\CreateAiApiKeyFormData;
use App\Data\UpdateAiApiKeyFormData;
use App\Models\AiApiKey;
use App\Repository\AiApiKeyRepository;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AiApiKeyUiController
{
    public function index(AiApiKeyRepository $aiApiKeyRepository): \Inertia\Response
    {
        return Inertia::render('ApiKeysPage', [
            'useForGeneration' => AiApiKeyData::optional(
                $aiApiKeyRepository->getAiApiKeyForGeneration()
            )
        ]);
    }

    public function createKey(CreateAiApiKeyFormData $aiApiKeyFormData) {
        AiApiKey::create([
            'name' => $aiApiKeyFormData->name,
            'key' => $aiApiKeyFormData->key,
            'ai_vendor' => $aiApiKeyFormData->aiVendor,
            'user_id' => Auth::user()->id,
        ]);

        return redirect()->back();
    }

    public function updateKey(
        UpdateAiApiKeyFormData $aiApiKeyFormData,
        AiApiKey              $aiApiKey = null,
    ) {
        $aiApiKey->update(array_filter([
            'name' => $aiApiKeyFormData->name,
            'use_for_generation' => $aiApiKeyFormData->useForGeneration,
            'default_model' => $aiApiKeyFormData->defaultModel,
        ], fn($value) => !is_null($value)));

        return redirect()->back();
    }
}
