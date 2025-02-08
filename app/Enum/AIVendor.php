<?php

namespace App\Enum;

enum AIVendor: string
{
    case OPENAI = 'openai';
    case ANTHROPIC = 'anthropic';
}
