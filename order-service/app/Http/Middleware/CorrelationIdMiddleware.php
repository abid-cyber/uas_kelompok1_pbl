<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CorrelationIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generate atau ambil Correlation ID dari header
        $correlationId = $request->header('X-Correlation-ID') ?? Str::uuid()->toString();
        
        // Set ke request
        $request->headers->set('X-Correlation-ID', $correlationId);
        
        // Set ke log context (untuk logging terdistribusi)
        Log::withContext(['correlation_id' => $correlationId]);
        
        // Process request
        $response = $next($request);
        
        // Set ke response
        $response->headers->set('X-Correlation-ID', $correlationId);
        
        return $response;
    }
}

