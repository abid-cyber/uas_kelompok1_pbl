<?php

namespace App\Http\Services;

use App\Exceptions\ServiceUnavailableException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class UserServiceClient
{
    private $client;
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.user_service.url', env('USER_SERVICE_URL', 'http://localhost:8000'));
        $this->client = new Client([
            'timeout' => 10,
            'connect_timeout' => 5,
        ]);
    }

    /**
     * Validate token dengan User Service
     *
     * @param string $token
     * @param string $correlationId
     * @return array
     * @throws ServiceUnavailableException
     */
    public function validateToken($token, $correlationId)
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/api/user/profile", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'X-Correlation-ID' => $correlationId,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (!isset($data['success']) || !$data['success']) {
                throw new ServiceUnavailableException('User Service', 'Token validation failed');
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('User Service call failed', [
                'error' => $e->getMessage(),
                'correlation_id' => $correlationId,
            ]);
            throw new ServiceUnavailableException('User Service', 'User service unavailable');
        }
    }

    /**
     * Get user by ID
     *
     * @param int $userId
     * @param string $token
     * @param string $correlationId
     * @return array
     * @throws ServiceUnavailableException
     */
    public function getUserById($userId, $token, $correlationId)
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/api/users/{$userId}", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'X-Correlation-ID' => $correlationId,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (!isset($data['success']) || !$data['success']) {
                throw new ServiceUnavailableException('User Service', 'User not found');
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('User Service call failed', [
                'error' => $e->getMessage(),
                'correlation_id' => $correlationId,
                'user_id' => $userId,
            ]);
            throw new ServiceUnavailableException('User Service', 'User service unavailable');
        }
    }
}

