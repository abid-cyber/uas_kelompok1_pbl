<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ApiExceptionHandler
{
    public static function handle(\Throwable $exception, Request $request): JsonResponse
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $exception->errors(),
            ], 422);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource tidak ditemukan',
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Method tidak diizinkan',
            ], 405);
        }

        return response()->json([
            'success' => false,
            'message' => $exception->getMessage() ?: 'Terjadi kesalahan pada server',
        ], 500);
    }
}
