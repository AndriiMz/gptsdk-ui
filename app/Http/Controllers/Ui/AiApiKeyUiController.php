<?php

namespace App\Http\Controllers\Ui;

use App\Data\CreateAiApiKeyFormData;
use App\Data\UpdateAiApiKeyFormData;
use App\Models\AiApiKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AiApiKeyUiController
{
    public function createKey(CreateAiApiKeyFormData $aiApiKeyFormData) {
        AiApiKey::create([
            'name' => $aiApiKeyFormData->name,
            'key' => $aiApiKeyFormData->key,
            'ai_vendor' => $aiApiKeyFormData->aiVendor,
            'user_id' => Auth::user()->id
        ]);

        return redirect()->back();
    }

    public function updateKey(
        UpdateAiApiKeyFormData $aiApiKeyFormData,
        AiApiKey              $aiApiKey = null,
    ) {
        $aiApiKey->update(['name' => $aiApiKeyFormData->name]);

        return redirect()->back();
    }
}
