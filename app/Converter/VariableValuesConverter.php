<?php

namespace App\Converter;

use App\Enum\VariableType;
use App\Models\Repository;
use App\Providers\PromptRepositoryProvider;

class VariableValuesConverter
{
    public function convert(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $repository,
        array $variableValues,
        array $variables
    ): array {
        $variablesByName = array_column($variables, 'type', 'name');
        $promptRepository = $promptRepositoryProvider->getPromptRepository($repository->type->value);

        foreach ($variableValues as $variableName => $variableValue) {
            if ($variablesByName[$variableName] === VariableType::FILE->value) {
                $variableValues[$variableName] = json_encode(
                    $promptRepository->getFile(
                        $repository->token,
                        $repository->owner,
                        $repository->name,
                        $variableValue
                    )?->content ?? ''
                );
            }
        }

        return $variableValues;
    }
}
