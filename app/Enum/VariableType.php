<?php

namespace App\Enum;

enum VariableType: string
{
    case STRING = 'string';
    case INT = 'int';
    case FLOAT = 'float';
    case FILE = 'file';
    case TEXT = 'text';
}
