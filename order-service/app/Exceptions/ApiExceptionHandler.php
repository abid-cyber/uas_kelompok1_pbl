<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ApiExceptionHandler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            // Handle validation exceptions
            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                    'correlation_id' => $request->header('X-Correlation-ID'),
                ], 422);
            }

            // Handle service unavailable exceptions
            if ($e instanceof ServiceUnavailableException) {
                return $e->render($request);
            }

            // Handle HTTP exceptions
            if ($e instanceof HttpException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'An error occurred',
                    'correlation_id' => $request->header('X-Correlation-ID'),
                ], $e->getStatusCode());
            }

            // Handle other exceptions
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'correlation_id' => $request->header('X-Correlation-ID'),
            ], 500);
        }

        return parent::render($request, $e);
    }
}

