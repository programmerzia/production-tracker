<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateProductManager()
    {
        $user = User::factory()->create(['role' => 'product_manager']);
        $this->actingAs($user);
        return $user;
    }

    /** @test */
    public function guests_cannot_access_products()
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(401); // Unauthenticated
    }

    /** @test */
    public function non_product_managers_cannot_access_products()
    {
        $user = User::factory()->create(['role' => 'engineer']); // unauthorized role
        $this->actingAs($user);

        $response = $this->getJson('/api/products');
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function product_manager_can_list_products()
    {
        $this->authenticateProductManager();

        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');
        $response->assertOk()->assertJsonCount(3);
    }

    /** @test */
    public function product_manager_can_create_a_product()
    {
        $this->authenticateProductManager();

        $payload = [
            'name' => 'Test Product',
            'version' => '1.0',
            'description' => 'Test description',
            'price' => 99.99,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertCreated()->assertJsonFragment(['name' => 'Test Product']);
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    /** @test */
    public function product_manager_can_update_a_product()
    {
        $this->authenticateProductManager();

        $product = Product::factory()->create();

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Name',
            'version' => $product->version,
            'description' => $product->description,
            'price' => $product->price,
        ]);

        $response->assertOk()->assertJsonFragment(['name' => 'Updated Name']);
    }

    /** @test */
    public function product_manager_can_delete_a_product()
    {
        $this->authenticateProductManager();

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
