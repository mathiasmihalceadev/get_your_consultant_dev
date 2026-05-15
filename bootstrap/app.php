<?php

require_once __DIR__.'/../app/Support/browsershot_polyfill.php';

use App\Exceptions\OpenAIJsonException;
use App\Exceptions\OpenAIRequestException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            \App\Http\Middleware\SetLocale::class,
        ], append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\ApplySeoIndexingHeaders::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (OpenAIRequestException $e) {
            Log::channel('report')->error('OpenAIRequestException', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        });

        $exceptions->reportable(function (OpenAIJsonException $e) {
            Log::channel('report')->error('OpenAIJsonException', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        });
    })->create();
