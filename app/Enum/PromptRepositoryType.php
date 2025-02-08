<?php

namespace App\Enum;

enum PromptRepositoryType: string
{
    case GITHUB = 'github';
    case TEMP = 'temp';
}
