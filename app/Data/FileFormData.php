<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class FileFormData extends Data
{
    public const FILE_NAME_REGEX = '/^\/?[^\/\0]+(\/[^\/\0]+)*\.(prompt|md)$/';

    public function __construct(
        public readonly string $content,
        #[Required]
        #[Regex(self::FILE_NAME_REGEX)]
        public readonly string $path,
        public readonly ?string $sha = null,
    )
    {}
}
