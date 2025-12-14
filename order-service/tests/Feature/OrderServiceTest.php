<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set service URLs for testing
        config(['services.user_service.url' => 'http://localhost:8000']);
        config(['services.product_service.url' => 'http://localhost:8001']);
    }

    /**
     * Test creating order with inter-service calls
     */
    public function test_can_create_order_with_inter_service_calls(): void
    {
        // Mock User Service responses
        Http::fake([
            'localhost:8000/api/user/profile' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ],
            ], 200),
            
            'localhost:8000/api/users/1' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ],
            ], 200),
        ]);

        // Mock Product Service responses
        Http::fake([
            'localhost:8001/api/products/1' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'name' => 'Product 1',
                    'price' => 50000,
                    'stock' => 100,
                ],
            ], 200),
            
            'localhost:8001/api/products/1/stock' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'name' => 'Product 1',
                    'stock' => 98,
                ],
            ], 200),
        ]);

        $correlationId = 'test-correlation-id-' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
            'X-Correlation-ID' => $correlationId,
            'Accept' => 'application/json',
        ])->postJson('/api/orders', [
            'user_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'price' => 50000,
                ],
            ],
            'total' => 100000,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Order created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'items',
                    'total',
                    'status',
                    'created_at',
                    'updated_at',
                ],
                'correlation_id',
            ]);

        // Verify correlation ID is returned
        $this->assertEquals($correlationId, $response->json('correlation_id'));

        // Verify order was created in database
        $this->assertDatabaseHas('orders', [
            'user_id' => 1,
            'status' => 'completed',
        ]);
    }

    /**
     * Test order creation fails when user service is unavailable
     */
    public function test_order_creation_fails_when_user_service_unavailable(): void
    {
        // Mock User Service to return error
        Http::fake([
            'localhost:8000/api/user/profile' => Http::response([], 503),
        ]);

        $correlationId = 'test-correlation-id-' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
            'X-Correlation-ID' => $correlationId,
            'Accept' => 'application/json',
        ])->postJson('/api/orders', [
            'user_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'price' => 50000,
                ],
            ],
            'total' => 100000,
        ]);

        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
                'service' => 'User Service',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'service',
                'correlation_id',
            ]);

        // Verify correlation ID is returned
        $this->assertEquals($correlationId, $response->json('correlation_id'));
    }

    /**
     * Test order creation fails when product service is unavailable
     */
    public function test_order_creation_fails_when_product_service_unavailable(): void
    {
        // Mock User Service to succeed
        Http::fake([
            'localhost:8000/api/user/profile' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'name' => 'Test User'],
            ], 200),
            
            'localhost:8000/api/users/1' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'name' => 'Test User'],
            ], 200),
        ]);

        // Mock Product Service to return error
        Http::fake([
            'localhost:8001/api/products/1' => Http::response([], 503),
        ]);

        $correlationId = 'test-correlation-id-' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
            'X-Correlation-ID' => $correlationId,
            'Accept' => 'application/json',
        ])->postJson('/api/orders', [
            'user_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'price' => 50000,
                ],
            ],
            'total' => 100000,
        ]);

        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
                'service' => 'Product Service',
            ]);
    }

    /**
     * Test order creation fails when stock is insufficient
     */
    public function test_order_creation_fails_when_stock_insufficient(): void
    {
        // Mock User Service
        Http::fake([
            'localhost:8000/api/user/profile' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'name' => 'Test User'],
            ], 200),
            
            'localhost:8000/api/users/1' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'name' => 'Test User'],
            ], 200),
        ]);

        // Mock Product Service with low stock
        Http::fake([
            'localhost:8001/api/products/1' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'name' => 'Product 1',
                    'stock' => 1, // Only 1 in stock
                ],
            ], 200),
        ]);

        $correlationId = 'test-correlation-id-' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
            'X-Correlation-ID' => $correlationId,
            'Accept' => 'application/json',
        ])->postJson('/api/orders', [
            'user_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2, // Requesting 2 but only 1 available
                    'price' => 50000,
                ],
            ],
            'total' => 100000,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonFragment([
                'message' => 'Insufficient stock for product ID: 1',
            ]);
    }

    /**
     * Test validation errors
     */
    public function test_order_creation_validates_request(): void
    {
        $correlationId = 'test-correlation-id-' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
            'X-Correlation-ID' => $correlationId,
            'Accept' => 'application/json',
        ])->postJson('/api/orders', [
            // Missing required fields
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
                'correlation_id',
            ]);
    }

    /**
     * Test correlation ID is generated if not provided
     */
    public function test_correlation_id_is_generated_if_not_provided(): void
    {
        // Mock services
        Http::fake([
            'localhost:8000/api/user/profile' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'name' => 'Test User'],
            ], 200),
            
            'localhost:8000/api/users/1' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'name' => 'Test User'],
            ], 200),
            
            'localhost:8001/api/products/1' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'name' => 'Product 1', 'stock' => 100],
            ], 200),
            
            'localhost:8001/api/products/1/stock' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'stock' => 98],
            ], 200),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer fake-token',
            'Accept' => 'application/json',
        ])->postJson('/api/orders', [
            'user_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'price' => 50000,
                ],
            ],
            'total' => 100000,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'correlation_id',
            ]);

        // Verify correlation ID is present in response
        $this->assertNotEmpty($response->json('correlation_id'));
    }
}

