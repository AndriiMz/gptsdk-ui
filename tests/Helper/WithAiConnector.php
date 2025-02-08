<?php

namespace Tests\Helper;

use App\Models\AiApiKey;
use App\Models\AiConnector;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

trait WithAiConnector
{
    use WithAuthorizedUser, MakesHttpRequests, WithAIApiKey;

    protected AiConnector $aiConnector;

    protected function withAiConnector()
    {
        $this->withAIApiKey();

        $response = $this->post(
            '/ui_api/ai_connector',
            [
                'llmOptions' => ['model' => 'gpt-4o'],
                'aiApiKeyId' => $this->aiApiKey->id
            ]
        );

        $this->aiConnector = AiConnector::where('user_id', $this->user->id)->first();

        return $response;
    }

}
