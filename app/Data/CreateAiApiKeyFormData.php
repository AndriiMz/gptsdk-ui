<?php

namespace App\Data;

use App\ValidationAttribute\AIApiKey;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class CreateAiApiKeyFormData extends Data
{
    public function __construct(
        public readonly string $aiVendor,
        #[AIApiKey(aiVendorFieldName: 'aiVendor')]
        public readonly string $key,
        #[Min(1)]
        public readonly string $name
    ) {

    }
}
