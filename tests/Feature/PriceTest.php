<?php

namespace Tests\Feature;

use App\DTOs\PriceDTO;
use App\DTOs\PriceUpdateDTO;
use App\Http\Controllers\PriceController;
use App\Models\Price;
use App\Models\Product;
use App\Models\Discount;
use App\Services\PriceService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response as AccessResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;

class PriceTest extends TestCase
{
    use RefreshDatabase;
     /////////////////php artisan test --filter=PriceTest/////////////
    /** @var \Mockery\MockInterface|\App\Services\PriceService */
    protected $priceService;

    protected function setUp(): void
    {             
        parent::setUp();

        // Mock del servicio
        $this->priceService = Mockery::mock(PriceService::class);
        $this->app->instance(PriceService::class, $this->priceService);

        // Mock parcial de Gate
        Gate::partialMock();

        // Rutas con prefijo /api
        Route::prefix('api')->middleware('api')->group(function () {
            Route::apiResource('prices', PriceController::class);
        });
    }

    /** Helpers */
    private function allowAbility(string $ability): void
    {
        Gate::shouldReceive('authorize')
            ->with($ability, Price::class)
            ->andReturn(AccessResponse::allow());
    }

    private function denyAbility(string $ability): void
    {
        Gate::shouldReceive('authorize')
            ->with($ability, Price::class)
            ->andThrow(AuthorizationException::class);
    }

    /** INDEX */

    public function test_index_returns_all_prices_when_authorized(): void
    {
        $this->allowAbility('viewAny');

        $expected = [
            ['id' => '1', 'name' => 'Basic', 'price' => 10.0],
            ['id' => '2', 'name' => 'Pro',   'price' => 20.0],
        ];

        $this->priceService->shouldReceive('getAllPrices')->once()->andReturn($expected);

        $res = $this->getJson('api/prices');

        $res->assertOk()->assertExactJson($expected);
    }

    public function test_index_filters_by_name_when_query_param_present(): void
    {
        $this->allowAbility('viewAny');

        $expected = [
            ['id' => '2', 'name' => 'Pro', 'price' => 20.0],
        ];

        $this->priceService
            ->shouldReceive('findPriceByName')
            ->once()
            ->with('Pro')
            ->andReturn($expected);

        $res = $this->getJson('api/prices?name=Pro');

        $res->assertOk()->assertExactJson($expected);
    }

    public function test_index_unauthorized_returns_403(): void
    {
        $this->denyAbility('viewAny');

        $res = $this->getJson('api/prices');

        $res->assertStatus(403);
    }

    /** STORE */

    public function test_store_creates_price_when_authorized(): void
    {
        $this->allowAbility('create');

        // Requisitos del PriceDTO: product_id y discount_id deben existir
        $product  = Product::factory()->create();
        $discount = Discount::factory()->create();

        $payload = [
            'name'        => 'Pro',
            'product_id'  => $product->id,
            'description' => 'Plan Pro mensual',
            'quantity'    => 1,
            'price'       => 25.5,
            'discount_id' => $discount->id,
            'status'    => 'active',
        ];

        // Lo que el servicio devolvería (ajústalo a tu caso real si devuelves más campos)
        $created = [
            'id'          => '123',
            'name'        => 'Pro',
            'product_id'  => $product->id,
            'description' => 'Plan Pro mensual',
            'quantity'    => 1,
            'price'       => 25.5,
            'discount_id' => $discount->id,
            'status'      => 'active',
        ];

        $this->priceService
            ->shouldReceive('createPrice')
            ->once()
            ->with(Mockery::on(function ($dto) use ($payload) {
                return $dto instanceof PriceDTO
                    && data_get($dto, 'name') === $payload['name']
                    && (int) data_get($dto, 'product_id') === (int) $payload['product_id']
                    && data_get($dto, 'description') === $payload['description']
                    && (int) data_get($dto, 'quantity') === (int) $payload['quantity']
                    && (float) data_get($dto, 'price') === (float) $payload['price']
                    && (int) data_get($dto, 'discount_id') === (int) $payload['discount_id'];
            }))
            ->andReturn($created);

        $res = $this->postJson('api/prices', $payload);

        $res->assertOk()->assertExactJson($created);
    }

    public function test_store_unauthorized_returns_403(): void
    {
        $this->denyAbility('create');

        $product  = Product::factory()->create();
        $discount = Discount::factory()->create();

        $res = $this->postJson('api/prices', [
            'name'        => 'Pro',
            'product_id'  => $product->id,
            'description' => 'Plan Pro mensual',
            'quantity'    => 1,
            'price'       => 25.5,
            'discount_id' => $discount->id,
        ]);

        $res->assertStatus(403);
    }

    /** SHOW */

    public function test_show_returns_price_when_authorized(): void
    {
        $this->allowAbility('view');

        $id = 'abc';
        $expected = ['id' => $id, 'name' => 'Basic', 'price' => 10.0];

        $this->priceService->shouldReceive('getPriceById')->once()->with($id)->andReturn($expected);

        $res = $this->getJson("api/prices/{$id}");

        $res->assertOk()->assertExactJson($expected);
    }

    public function test_show_unauthorized_returns_403(): void
    {
        $this->denyAbility('view');

        $res = $this->getJson('api/prices/abc');

        $res->assertStatus(403);
    }

    /** UPDATE */

    public function test_update_modifies_price_when_authorized(): void
    {
        $this->allowAbility('update');

        $id = 123; // el DTO exige int
        $payload = [
            // En PriceUpdateDTO todos son opcionales; si los envías, se validan.
            'name'  => 'Basic+',
            'price' => 12.5,
            'description' => 'Nuevo nombre',
            'quantity'    => 2,
            // si envías product_id o discount_id, deben existir en BD
        ];

        $updated = [
            'id'          => $id,
            'name'        => 'Basic+',
            'description' => 'Nuevo nombre',
            'quantity'    => 2,
            'price'       => 12.5,
            'status'      => 'active',
        ];

        $this->priceService
            ->shouldReceive('updatePrice')
            ->once()
            ->with($id, Mockery::type(PriceUpdateDTO::class)) // matcher relajado
            ->andReturn($updated);

        $res = $this->putJson("api/prices/{$id}", $payload);

        $res->assertOk()->assertExactJson($updated);
    }

    public function test_update_unauthorized_returns_403(): void
    {
        $this->denyAbility('update');

        $res = $this->putJson('api/prices/123', ['name' => 'X']);

        $res->assertStatus(403);
    }

    /** DESTROY */

    public function test_destroy_deletes_price_when_authorized_and_ability_is_delete(): void
    {
        // Permitimos ambos por si aún existe el typo en el controlador
        Gate::shouldReceive('authorize')->with('delete', Price::class)->andReturn(AccessResponse::allow());
        Gate::shouldReceive('authorize')->with('detele', Price::class)->andReturn(AccessResponse::allow());

        $id = 123;
        $expected = ['message' => 'deleted'];

        $this->priceService->shouldReceive('deletePrice')->once()->with($id)->andReturn($expected);

        $res = $this->deleteJson("api/prices/{$id}");

        $res->assertOk()->assertExactJson($expected);
    }

    public function test_destroy_fails_with_typo_in_ability_name_detele(): void
    {
        Gate::shouldReceive('authorize')->with('delete', Price::class)->andReturn(AccessResponse::allow());
        Gate::shouldReceive('authorize')->with('detele', Price::class)->andThrow(AuthorizationException::class);

        $res = $this->deleteJson('api/prices/123');

        $res->assertStatus(403);
    }
}
