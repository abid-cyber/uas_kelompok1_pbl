<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class ServiceUnavailableException extends Exception
{
    protected $serviceName;

    public function __construct($serviceName = 'Service', $message = 'Service temporarily unavailable', $code = 503, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->serviceName = $serviceName;
    }

    public function render(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'service' => $this->serviceName,
            'correlation_id' => $request->header('X-Correlation-ID'),
        ], $this->code);
    }
}

