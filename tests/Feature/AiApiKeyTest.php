<?php

namespace Feature;

use App\Enum\AIVendor;
use App\Models\AiApiKey;
use Tests\Helper\WithAIMock;
use Tests\Helper\WithAuthorizedUser;
use Tests\TestCase;

class AiApiKeyTest extends TestCase
{
    use WithAuthorizedUser, WithAIMock;

    public function testUpsert()
    {
        $this->mockAI();
        $user = $this->withAuthorizedUser();

        $response =  $this->post('/ai_api_key', [
            'name' => 'Key',
            'key' => '12345',
            'aiVendor' => AIVendor::OPENAI->value
        ]);

        $this->assertEquals(
            302,
            $response->status()
        );

        $aiApiKey = AiApiKey::where('user_id', $user->id)->first();
        $this->assertEquals('Key', $aiApiKey->name);
        $this->assertEquals('12345', $aiApiKey->key);
        $this->assertEquals(AIVendor::OPENAI->value, $aiApiKey->ai_vendor);

        $response =  $this->post("/ai_api_key/$aiApiKey->id", [
            'name' => 'NEW Key',
            'key' => 'new-key',
            'aiVendor' => AIVendor::OPENAI->value
        ]);

        $this->assertEquals(
            302,
            $response->status()
        );

        $aiApiKey = AiApiKey::find($aiApiKey->id);
        $this->assertEquals('NEW Key', $aiApiKey->name);
        $this->assertEquals('12345', $aiApiKey->key);
        $this->assertEquals(AIVendor::OPENAI->value, $aiApiKey->ai_vendor);
    }

    public function testDelete()
    {
        $user = $this->withAuthorizedUser();
        $this->mockAI();

        $response =  $this->post('/ai_api_key', [
            'name' => 'Key',
            'key' => '12345',
            'aiVendor' => AIVendor::OPENAI->value
        ]);

        $this->assertEquals(
            302,
            $response->status()
        );

        $aiApiKey = AiApiKey::where('user_id', $user->id)->first();
        $this->delete("/ui_api/ai_api_key/$aiApiKey->id");

        $this->assertCount(
            0,
            AiApiKey::where('user_id', $user->id)->get()
        );
    }

    public function testList()
    {
        $this->withAuthorizedUser();
        $this->mockAI();

        $this->post('/ai_api_key', [
            'name' => 'Key',
            'key' => '12345',
            'aiVendor' => AIVendor::OPENAI->value
        ]);

        $response = $this->get('/ui_api/ai_api_key');
        $json = $response->json();
        $this->assertCount(1, $json['aiApiKeys']);
    }
}
