<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->category = Category::create(['name' => 'Test Category']);
        $this->supplier = Supplier::create(['name' => 'Test Supplier']);
    }

    public function test_can_create_product(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 10000,
            'stock' => 50,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'stock',
                    'category_id',
                    'supplier_id',
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 10000,
            'stock' => 50,
        ]);
    }

    public function test_can_get_product_by_id(): void
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 10000,
            'stock' => 50,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => 'Test Product',
                ],
            ]);
    }

    public function test_can_get_list_of_products(): void
    {
        Product::create([
            'name' => 'Product 1',
            'price' => 10000,
            'stock' => 50,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        Product::create([
            'name' => 'Product 2',
            'price' => 20000,
            'stock' => 30,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ]);
    }

    public function test_can_update_product(): void
    {
        $product = Product::create([
            'name' => 'Original Product',
            'price' => 10000,
            'stock' => 50,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'price' => 15000,
            'stock' => 75,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJson([
                'data' => [
                    'name' => 'Updated Product',
                    'price' => 15000,
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
        ]);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::create([
            'name' => 'Product to Delete',
            'price' => 10000,
            'stock' => 50,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_validation_fails_for_invalid_data(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => '', // Empty name
            'price' => -100, // Negative price
            'stock' => -10, // Negative stock
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);
    }

    public function test_returns_404_for_nonexistent_product(): void
    {
        $response = $this->getJson('/api/products/99999');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }
}

