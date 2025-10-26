<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response as AccessResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock parcial de Gate para controlar autorizaciones en cada prueba
        Gate::partialMock();

        // Registrar rutas API para ProductController (usa las rutas reales durante las pruebas)
        Route::prefix('api')->middleware('api')->group(function () {
            Route::apiResource('products', ProductController::class);
        });
    }

    private function allowAbility(string $ability): void
    {
        Gate::shouldReceive('authorize')
            ->with($ability, Product::class)
            ->andReturn(AccessResponse::allow());
    }

    private function denyAbility(string $ability): void
    {
        Gate::shouldReceive('authorize')
            ->with($ability, Product::class)
            ->andThrow(new AuthorizationException());
    }

    public function test_index_returns_products_when_authorized(): void
    {
        $this->allowAbility('viewAny');

        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_index_unauthorized_returns_403(): void
    {
        $this->denyAbility('viewAny');

        $response = $this->getJson('/api/products');

        $response->assertStatus(403);
    }

    public function test_show_returns_product_when_authorized(): void
    {
        $this->allowAbility('view');

        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $product->id, 'name' => $product->name]);
    }

    public function test_store_creates_product_when_authorized(): void
    {
        $this->allowAbility('create');

        $unit = Unit::factory()->create();

        $payload = [
            'name' => 'Producto Test',
            'sku' => 'SKU-12345',
            'description' => 'Descripción de prueba',
            'imagen' => 'https://example.com/image.jpg',
            'available_quantity' => 10,
            'warehouse_quantity' => 20,
            'unit_id' => $unit->id,
            'status' => 'active',
            'unit_cost' => 50,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Producto Test', 'sku' => 'SKU-12345']);

        $this->assertDatabaseHas('products', ['name' => 'Producto Test', 'sku' => 'SKU-12345']);
    }

    public function test_store_unauthorized_returns_403(): void
    {
        $this->denyAbility('create');

        $unit = Unit::factory()->create();

        $payload = [
            'name' => 'Producto Test',
            'unit_id' => $unit->id,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(403);
    }

    public function test_update_modifies_product_when_authorized(): void
    {
        $this->allowAbility('update');

        $product = Product::factory()->create([
            'name' => 'Nombre viejo',
            'sku' => 'OLD-SKU'
        ]);

        $update = [
            'name' => 'Nombre nuevo',
            'sku' => 'NEW-SKU'
        ];

        $response = $this->putJson("/api/products/{$product->id}", $update);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Nombre nuevo', 'sku' => 'NEW-SKU']);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Nombre nuevo', 'sku' => 'NEW-SKU']);
    }

    public function test_destroy_deletes_product_when_authorized(): void
    {
        $this->allowAbility('delete');

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
