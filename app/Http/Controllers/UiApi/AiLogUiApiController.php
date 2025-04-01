<?php

namespace App\Http\Controllers\UiApi;

use App\Data\AiLogData;
use App\Http\Controllers\Controller;
use App\Models\AiLog;
use App\Models\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiLogUiApiController extends Controller
{
    public function getLogs(
        Repository $paidRepository,
        string $path,
        Request $request,
    ) {
        $aiLogs = AiLog
            ::where('path', $path)
            ->where('user_id', Auth::user()->id)
            ->where('repository_id', $paidRepository->id)
            ->where('created_at', '<', $request->get('date_after', new \DateTime()))
            ->limit(5)
            ->get();

        return new JsonResponse([
            'logs' => AiLogData::collect($aiLogs)
        ]);
    }
}
