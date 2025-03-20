<?php

use App\Exception\ExpiredApiKeyException;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(
            function (ExpiredApiKeyException $e, Request $request) {
                if ($request->is('/ui_api/*')) {
                    return response()->json([
                        'message' => 'Expired API key. Visit repository settings.',
                    ], 400);
                }

                $repository = $request->route('repository');
                if (null === $repository) {
                    $repository = $request->route('paidRepository');
                }

                return Inertia::render(
                    'ExpiredApiKeyPage',
                    [
                        'repositoryId' => $repository?->id
                    ]
                );
            }
        );
    })->create();
