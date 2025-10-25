<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unit = Unit::factory()->create();
        return [
        'name' => fake()->name(),
        'sku' =>fake()->phoneNumber(),
        'description' => fake()->text(),
        'imagen' =>fake()->imageUrl(),
        'available_quantity' =>fake()->numberBetween(1,30),
        'warehouse_quantity'=>fake()->numberBetween(31,100),
        'unit_id' => $unit->id,
        'status' => 'active',
        'unit_cost'=>fake()->numberBetween(20,100)
        ];
    }
}