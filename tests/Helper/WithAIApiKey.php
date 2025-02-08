<?php

namespace Tests\Helper;

use App\Enum\AIVendor;
use App\Models\AiApiKey;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

trait WithAIApiKey
{
    use WithAuthorizedUser, WithAIMock, MakesHttpRequests;

    protected AiApiKey $aiApiKey;

    protected function withAIApiKey()
    {
        $this->mockAI();
        $this->withAuthorizedUser();

        $this->post('/ai_api_key', [
            'name' => 'Key',
            'key' => '12345',
            'aiVendor' => AIVendor::OPENAI->value
        ]);

        $this->aiApiKey = AiApiKey::where('user_id', $this->user->id)->first();
    }
}
