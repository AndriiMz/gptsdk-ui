<?php

namespace App\ValidationAttribute;

use App\Rules\AIApiKeyRule;
use \Attribute;
use Spatie\LaravelData\Attributes\Validation\CustomValidationAttribute;
use Spatie\LaravelData\Support\Validation\ValidationPath;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class AIApiKey extends CustomValidationAttribute
{
    public function __construct(private readonly string $aiVendorFieldName)
    {}

    /**
     * @return array<object|string>|object|string
     */
    public function getRules(ValidationPath $path): array|object|string
    {
        return [new AIApiKeyRule(
            $this->aiVendorFieldName
        )];
    }
}
