<?php

namespace Tests\Feature;

use App\Models\Price;
use App\Models\Product;
use App\Models\Discount;
use App\Models\Role;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_price(): void
    {
        // 🔐 Auth (si tu endpoint lo requiere)
        $role = Role::factory()->create(['name' => 'admin', 'description' => 'admin']);
        $user = User::factory()->create([
            'email' => 'test@ejemplo.com',
            'password' => Hash::make('password123'),
            'name' => 'Admin',
            'last_name' => 'test',
            'rol_id' => $role->id
        ]);
        Sanctum::actingAs($user);

        $product = Product::factory()->create();
        $discount = Discount::factory()->create();

        $payload = [
            'product_id' => $product->id,
            'name' => 'Paquete Store',
            'description' => 'Desc Store',
            'quantity' => 3,
            'price' => 1500,
            'discount_id' => $discount->id,
            'status' => 'active',
        ];

        $res = $this->postJson('api/prices', $payload);

        $res->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'content' => ['product_id', 'name', 'price']
            ]);

        dump($res->json());
    }

    public function test_show_returns_price(): void
    {
        $product = Product::factory()->create();
        $discount = Discount::factory()->create();

        $price = Price::factory()->create([
            'product_id' => $product->id,
            'name' => 'Paquete Store',
            'description' => 'Desc Store',
            'quantity' => 3,
            'price' => 1500,
            'discount_id' => $discount->id,
            'status' => 'active',
        ]);

        $res = $this->getJson("api/prices/{$price->id}");

        dump($res->json());


        $res->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'content' => ['product_id', 'name', 'price']
            ]);
    }
}
