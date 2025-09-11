<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    public function test_get_products(): void
    {
        $unit = Unit::factory()->create([
            'name' => 'libras',
            'description' => 'primera unidad',
        ]);
        $product = Product::factory()->create([
            'name' => 'producto 1',
            'sku' => '001',
            'description' => 'primer producto de todos',
            'imagen' => 'imagendeejemplo',
            'available_quantity' => '100',
            'warehouse_quantity' => '102',
            'unit_id' => 1,
            'status' => 'active'
        ]);

        dump($product);

        $this->assertEquals('producto 1', $product->name);
        $this->assertEquals('001', $product->sku);
        $this->assertEquals('active', $product->status);
    }
}
