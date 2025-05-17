<?php

use App\Data\RepositoryData;

use App\Http\Controllers\Ui\AiApiKeyUiController;
use App\Http\Controllers\Ui\LogtoUiController;
use App\Http\Controllers\Ui\RepositoryUiController;
use App\Http\Controllers\Ui\PromptUiController;

use App\Http\Controllers\UiApi\AiApiKeyUiApiController;
use App\Http\Controllers\UiApi\AiConnectorUiApiController;
use App\Http\Controllers\UiApi\AiVariableValueUiApiController;
use App\Http\Controllers\UiApi\AiLogUiApiController;
use App\Http\Controllers\UiApi\PromptUiApiController;
use App\Http\Controllers\UiApi\MockUiApiController;
use App\Http\Controllers\UiApi\RepositoryUiApiController;
use App\Models\Repository;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Logto\Sdk\LogtoClient;
use Logto\Sdk\InteractionMode;

Route::get('/callback', [LogtoUiController::class, 'callback']);
Route::get('/login', function (LogtoClient $client) {
    return redirect($client->signIn(config('app.url') . '/callback'));
})->name('login');

Route::get('/signup', function (LogtoClient $client) {
    return redirect($client->signIn(
        config('app.url') . '/callback',
        InteractionMode::signUp
    ));
})->name('signup');

Route::get('/logout', function (LogtoClient $client) {
    return redirect($client->signOut(config('app.url')));
})->name('logout');


Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return Inertia::render('DashboardPage');
    })->name('home');

    Route::get('/billing', function (Request $request) {
        return $request->user()->redirectToBillingPortal();
    });

    Route::get(
        '/repository/{paidRepository}/prompt/{path?}',
        [PromptUiController::class, 'edit']
    )->where('path', '(.*)');

    Route::get(
        '/repository/{repository}/prompts/{path?}',
        [PromptUiController::class, 'list']
    )->where('path', '(.*)')->name('prompts');

    Route::get('/ai_api_key', fn() => Inertia::render('ApiKeysPage'));
    Route::post('/ai_api_key', [AiApiKeyUiController::class, 'createKey']);
    Route::post('/ai_api_key/{aiApiKey}', [AiApiKeyUiController::class, 'updateKey']);

    Route::get('/repository/new', function () {
        return Inertia::render('RepositoryPage');
    });

    Route::get('/repository/{repository}/settings', function (Repository $repository) {
        return Inertia::render('RepositoryPage', [
            'repository' => RepositoryData::from($repository),
        ]);
    });

    Route::post('/repository', [RepositoryUiController::class, 'upsertRepository']);
    Route::post('/repository/{repository}', [RepositoryUiController::class, 'upsertRepository']);
    Route::get('/repository/{repository}/purchase', [RepositoryUiController::class, 'purchaseRepository']);

    Route::post('/repository/{paidRepository}/prompt', [PromptUiController::class, 'upsertPrompt']);
    Route::post('/repository/{paidRepository}/prompt/validate', [PromptUiController::class, 'validate']);
});


Route::prefix('ui_api')->group(function () {
    Route::delete('/repository/{repository}', [RepositoryUiApiController::class, 'delete']);

    Route::post(
        '/repository/{paidRepository}/prompt/result/{path?}',
        [PromptUiApiController::class, 'getPromptResults']
    )->where('path', '(.*)');

    Route::post(
        '/repository/{paidRepository}/prompt/render/{path?}',
        [PromptUiApiController::class, 'renderPrompt']
    )->where('path', '(.*)');


    Route::get(
        '/repository/{paidRepository}/prompt/ai_logs/{path?}',
        [AiLogUiApiController::class, 'getLogs']
    )->where('path', '(.*)');

    Route::post(
        '/repository/{paidRepository}/prompt/mock/{path?}',
        [MockUiApiController::class, 'upsertMock']
    )->where('path', '(.*)');

    Route::get(
        '/repository/{paidRepository}/prompt/mock/{path?}',
        [MockUiApiController::class, 'getMocks']
    )->where('path', '(.*)');

    Route::delete(
        '/repository/{paidRepository}/prompt/mock/{path?}',
        [MockUiApiController::class, 'deleteMock']
    )->where('path', '(.*)');


    Route::get('/ai_api_key/', [AiApiKeyUiApiController::class, 'getKeys']);
    Route::delete('/ai_api_key/{aiApiKey}', [AiApiKeyUiApiController::class, 'deleteKey']);

    Route::get('/ai_connector/', [AiConnectorUiApiController::class, 'getAiConnectors']);
    Route::post('/ai_connector', [AiConnectorUiApiController::class, 'upsertAiConnector']);
    Route::post('/ai_connector/{aiConnector}', [AiConnectorUiApiController::class, 'upsertAiConnector']);
    Route::delete('/ai_connector/{aiConnector}', [AiConnectorUiApiController::class, 'deleteAiConnector']);


    Route::get('/repository/{paidRepository}/variable_values/{path}', [AiVariableValueUiApiController::class, 'getVariableValues']);
    Route::post('/repository/{paidRepository}/variable_values', [AiVariableValueUiApiController::class, 'upsertVariableValues']);
    Route::post(
        '/repository/{paidRepository}/variable_values/{aiVariableValue}',
        [AiVariableValueUiApiController::class, 'upsertVariableValues']
    );
    Route::delete(
        '/repository/{paidRepository}/variable_values/{aiVariableValue}',
        [AiVariableValueUiApiController::class, 'deleteVariableValues']
    );

    Route::delete(
        '/repository/{paidRepository}/prompt/{path?}',
        [PromptUiApiController::class, 'deletePrompt']
    )->where('path', '(.*)');
})->middleware('auth');

