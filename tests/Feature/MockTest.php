<?php

namespace Feature;

use Tests\Helper\WithAIApiKey;
use Tests\Helper\WithAiConnector;
use Tests\Helper\WithAIMock;
use Tests\Helper\WithRepository;
use Tests\Helper\WithAuthorizedUser;
use Tests\TestCase;

class MockTest extends TestCase
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
        $variableValues = [['who' => 'moroz']];
        $promptPath = 'prompt1.prompt';
        // Creates prompt
        $this->post(
            "/repository/$repositoryId/prompt",
            [
                'content' => $promptContent,
                'path' => $promptPath,
                'sha' => ''
            ]
        );

        // Test prompt
        $this->mockAI();
        $response = $this->post(
            "/ui_api/repository/$repositoryId/prompt/result/$promptPath",
            [
                'variableValues' => $variableValues,
                'prompt' => $promptContent['messages'],
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


        $this->post(
            "/ui_api/repository/$repositoryId/prompt/mock/$promptPath",
            [
                'variableValues' => [['who' => 'moroz']],
                'output' =>  $json['logs'][0]['output']
            ]
        );

        $response = $this->get("/ui_api/repository/$repositoryId/prompt/mock/$promptPath");
        $json = $response->json();
        $this->assertCount(1, $json['mocks']);

        $this->delete("/ui_api/repository/$repositoryId/prompt/mock/$promptPath", [
            'hash' => array_keys($json['mocks'])[0]
        ]);

        $response = $this->get("/ui_api/repository/$repositoryId/prompt/mock/$promptPath");
        $json = $response->json();
        $this->assertCount(0, $json['mocks']);
    }
}
