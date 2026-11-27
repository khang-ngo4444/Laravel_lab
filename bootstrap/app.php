<?php

use App\Http\Middleware\AssignRequestId;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(AssignRequestId::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // API routes always answer errors as JSON, web routes keep the HTML error page
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson()
        );

        // Don't flood the log with the same failure: at most 100 reports/minute per exception type
        $exceptions->throttle(fn (Throwable $e) => Limit::perMinute(100)->by($e::class));

        // Meaningful API error body with the tracking ID (validation errors keep Laravel's format)
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'HTTP error',
                    'request_id' => Context::get('request_id'),
                ], $e->getStatusCode(), $e->getHeaders());
            }
        });
    })->create();
