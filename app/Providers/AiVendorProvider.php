<?php

namespace App\Providers;

use App\Enum\AIVendor;
use Gptsdk\AI\AnthropicAIVendor;
use Gptsdk\AI\OpenAIVendor;
use Gptsdk\Interfaces\AIVendor as AIVendorRunner;

class AiVendorProvider
{
    public function __construct(
        private readonly OpenAIVendor $openAIVendor,
        private readonly AnthropicAIVendor $anthropicAIVendor
    ) { }

    public function getByAiVendor(string $aiVendor): ?AIVendorRunner
    {
        return $this->getAllAiVendors()[$aiVendor] ?? null;
    }

    public function getDefaultLlmOptions(string $aiVendor): array
    {
        return [
            AIVendor::OPENAI->value => ['model' => 'gpt-3.5-turbo'],
            AIVendor::ANTHROPIC->value => [] //TODO: add anthropic model
        ][$aiVendor] ?? [];
    }

    public function getAllAiVendors(): array
    {
        return [
            AIVendor::OPENAI->value => $this->openAIVendor,
            AIVendor::ANTHROPIC->value => $this->anthropicAIVendor
        ];
    }
}
