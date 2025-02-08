<?php

namespace App\Rules;

use App\Providers\AiVendorProvider;
use Closure;
use Gptsdk\Types\AiRequest;
use Gptsdk\Types\PromptMessage;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\App;

class AIApiKeyRule implements ValidationRule, DataAwareRule
{
    protected $data = [];

    public function __construct(private readonly string $aiVendorFieldName)
    {

    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $aiVendorKey = $this->data[$this->aiVendorFieldName];
        /** @var AiVendorProvider $aiVendorProvider */
        $aiVendorProvider = App::make(AiVendorProvider::class);
        $aiVendor = $aiVendorProvider->getByAiVendor($aiVendorKey);
        $response = $aiVendor->complete(
            new AiRequest(
                apiKey: $value,
                aiVendor: $aiVendorKey,
                llmOptions: $aiVendorProvider->getDefaultLlmOptions($aiVendorKey),
                compiledMessages: [
                    new PromptMessage(
                        'user',
                        'Hello'
                    )
                ]
            )
        );

        if ($response->getStatusCode() !== 200) {
            $fail($response->toArray(false)['error']['message']);
        }
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }
}
