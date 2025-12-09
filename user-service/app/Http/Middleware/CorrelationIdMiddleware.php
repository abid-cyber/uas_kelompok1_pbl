<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class CorrelationIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = $request->header('X-Correlation-ID') ?? Str::uuid()->toString();
        
        $request->headers->set('X-Correlation-ID', $correlationId);
        
        $response = $next($request);
        
        $response->headers->set('X-Correlation-ID', $correlationId);
        
        return $response;
    }
}

