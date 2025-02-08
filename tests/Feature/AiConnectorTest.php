<?php

namespace Feature;

use Tests\Helper\WithAIApiKey;
use Tests\Helper\WithAiConnector;
use Tests\TestCase;

class AiConnectorTest extends TestCase
{
    use WithAIApiKey, WithAiConnector;

    public function testLifecycle()
    {
        $this->withAIApiKey();

        $response = $this->withAiConnector();
        $this->assertEquals(200, $response->getStatusCode());

        // Ai Connector created successfully
        $response = $this->get('/ui_api/ai_connector');
        $json = $response->json();
        $this->assertCount(1, $json['aiConnectors']);
        $this->assertEquals(['model' => 'gpt-4o'], $json['aiConnectors'][0]['llmOptions']);

        $aiConnectorId = $json['aiConnectors'][0]['id'];
        $response = $this->post(
            "/ui_api/ai_connector/$aiConnectorId",
            [
                'llmOptions' => ['model' => 'gpt-3.5-turbo'],
                'aiApiKeyId' => $this->aiApiKey->id
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());
        // Ai Connector updated successfully
        $response = $this->get('/ui_api/ai_connector');
        $json = $response->json();
        $this->assertCount(1, $json['aiConnectors']);
        $this->assertEquals(['model' => 'gpt-3.5-turbo'], $json['aiConnectors'][0]['llmOptions']);

        $response = $this->delete("/ui_api/ai_connector/$aiConnectorId");
        $this->assertEquals(200, $response->getStatusCode());

        // Ai Connector deleted successfully
        $response = $this->get('/ui_api/ai_connector');
        $json = $response->json();
        $this->assertCount(0, $json['aiConnectors']);
    }
}
