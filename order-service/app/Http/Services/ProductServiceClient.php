<?php

namespace App\Http\Services;

use App\Exceptions\ServiceUnavailableException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ProductServiceClient
{
    private $client;
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.product_service.url', env('PRODUCT_SERVICE_URL', 'http://localhost:8001'));
        $this->client = new Client([
            'timeout' => 10,
            'connect_timeout' => 5,
        ]);
    }

    /**
     * Get product by ID
     *
     * @param int $productId
     * @param string $correlationId
     * @return array
     * @throws ServiceUnavailableException
     */
    public function getProductById($productId, $correlationId)
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/api/products/{$productId}", [
                'headers' => [
                    'X-Correlation-ID' => $correlationId,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (!isset($data['success']) || !$data['success']) {
                throw new ServiceUnavailableException('Product Service', 'Product not found');
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('Product Service call failed', [
                'error' => $e->getMessage(),
                'correlation_id' => $correlationId,
                'product_id' => $productId,
            ]);
            throw new ServiceUnavailableException('Product Service', 'Product service unavailable');
        }
    }

    /**
     * Update stock after order
     *
     * @param int $productId
     * @param int $quantity
     * @param string $correlationId
     * @return array
     * @throws ServiceUnavailableException
     */
    public function updateStock($productId, $quantity, $correlationId)
    {
        try {
            $response = $this->client->put("{$this->baseUrl}/api/products/{$productId}/stock", [
                'headers' => [
                    'X-Correlation-ID' => $correlationId,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'quantity' => $quantity,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (!isset($data['success']) || !$data['success']) {
                throw new ServiceUnavailableException('Product Service', 'Failed to update stock');
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('Product Service stock update failed', [
                'error' => $e->getMessage(),
                'correlation_id' => $correlationId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
            throw new ServiceUnavailableException('Product Service', 'Product service unavailable');
        }
    }

    /**
     * Check stock availability
     *
     * @param int $productId
     * @param int $requiredQuantity
     * @param string $correlationId
     * @return bool
     * @throws ServiceUnavailableException
     */
    public function checkStock($productId, $requiredQuantity, $correlationId)
    {
        $product = $this->getProductById($productId, $correlationId);
        
        if (!isset($product['data']['stock']) || $product['data']['stock'] < $requiredQuantity) {
            return false;
        }

        return true;
    }
}

