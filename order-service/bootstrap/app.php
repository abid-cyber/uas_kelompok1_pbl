<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'correlation.id' => \App\Http\Middleware\CorrelationIdMiddleware::class,
        ]);
        
        $middleware->append(\App\Http\Middleware\CorrelationIdMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $e->errors(),
                        'correlation_id' => $request->header('X-Correlation-ID'),
                    ], 422);
                }

                if ($e instanceof \App\Exceptions\ServiceUnavailableException) {
                    return $e->render($request);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage() ?: 'An error occurred',
                        'correlation_id' => $request->header('X-Correlation-ID'),
                    ], $e->getStatusCode());
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                        'correlation_id' => $request->header('X-Correlation-ID'),
                    ], 500);
                }
            }
        });
    })->create();
