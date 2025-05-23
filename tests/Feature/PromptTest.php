<?php

namespace Feature;

use Tests\Helper\WithAIApiKey;
use Tests\Helper\WithAiConnector;
use Tests\Helper\WithAIMock;
use Tests\Helper\WithRepository;
use Tests\TestCase;

class PromptTest extends TestCase
{
    use WithRepository, WithAIMock, WithAIApiKey, WithAiConnector;

    public function testCreate()
    {
        $this->withRepository();
        $this->withAiConnector();

        $this->markRepsoitoryAsPaid();

        $repositoryId = $this->repository->id;
        $promptContent = [
            'messages' => [['role' => 'user', 'content' => 'hello {{who}}']],
            'variables' => [["name" => "who","type" => "string"]]
        ];
        $promptPath = 'prompt1.prompt';
        // Creates prompt
        $response = $this->post(
            "/repository/$repositoryId/file",
            [
                'content' => json_encode($promptContent),
                'path' => $promptPath,
                'sha' => ''
            ],
            ['accept' => 'application/json']
        );
        $response->assertStatus(201);

        // Test prompt
        $this->mockAI();
        $response = $this->post(
            "/ui_api/repository/$repositoryId/prompt/result/$promptPath",
            [
                'variableValues' => [['who' => 'moroz']],
                'prompt' => $promptContent,
                'aiConnectors' => [
                    [
                        'llmOptions' => $this->aiConnector->llm_options,
                        'aiApiKeyId' => $this->aiConnector->ai_api_key_id
                    ]
                ]
            ]
        );
        $json = $response->json();
        $this->assertEquals(
            'Hello Dude!',
            $json['logs'][0]['output'][0]['content']
        );

        //Check prompt logs
        $fiveMinuteAfter = (new \DateTime('+5 minute'))->format('Y-m-d H:i:s');
        $response = $this->get(
            "/ui_api/repository/$repositoryId/prompt/ai_logs/$promptPath?date_after=$fiveMinuteAfter"
        );
        $json = $response->json();
        $this->assertEquals(
            'Hello Dude!',
            $json['logs'][0]['output'][0]['content']
        );

        $json = $this->getRepositoryPrompts();
        $this->assertCount(1, $json['files']);

        //Deletes prompt
        $this->delete(
            "/ui_api/repository/$repositoryId/prompt/prompt1.prompt",
        );
        $json = $this->getRepositoryPrompts();
        $this->assertCount(1, $json['files']);
    }

}
