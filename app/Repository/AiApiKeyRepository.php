<?php

namespace App\Repository;

use App\Models\AiApiKey;
use Illuminate\Support\Facades\Auth;

class AiApiKeyRepository
{
    public function getAiApiKeyForGeneration(): ?AiApiKey
    {
        $user = Auth::user();

        return AiApiKey
            ::where('use_for_generation', true)
            ->where('user_id', $user->id)
            ->first();
    }
}
