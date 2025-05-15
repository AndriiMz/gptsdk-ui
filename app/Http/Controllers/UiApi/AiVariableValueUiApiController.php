<?php

namespace App\Http\Controllers\UiApi;

use App\Data\VariableValueData;
use App\Http\Controllers\Controller;
use App\Models\AiVariableValue;
use App\Models\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiVariableValueUiApiController extends Controller
{
    public function getVariableValues(Repository $paidRepository, string $path)
    {
        return response()->json(
            [
                'variableValues' => VariableValueData::collect(
                    AiVariableValue::query()
                        ->where('path', $path)
                        ->where('repository_id', $paidRepository->id)
                        ->where('user_id', Auth::user()->id)
                        ->get()
                )
            ]
        );
    }

    public function upsertVariableValues(
        Request $request,
        Repository $paidRepository,
        ?AiVariableValue $aiVariableValue
    ) {
        $validated = $request->validate([
            'path' => 'required|string',
            'variableValues' => 'required|array',
        ]);

        if ($aiVariableValue) {
            AiVariableValue::create(
                array_merge(
                    $validated,
                    [
                        'path' => $validated['path'],
                        'variable_values' => $validated['variableValues'],
                        'user_id' => Auth::user()->id,
                        'repository_id' => $paidRepository->id
                    ]
                )
            );
        } else {
            $aiVariableValue->update([
                'path' => $validated['path'],
                'variable_values' => $validated['variableValues'],
            ]);
        }

        return response()->json([],201);
    }

    public function deleteVariableValues(Repository $paidRepository, AiVariableValue $aiVariableValue)
    {
        $aiVariableValue->delete();

        return response()->json(null, 204);
    }
}
