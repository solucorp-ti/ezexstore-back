<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'product_serial' => 'P' . str_pad(fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'product_name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'base_price' => fake()->randomFloat(2, 10, 1000),
            'status' => fake()->randomElement(['active', 'inactive', 'discontinued']),
            'unit_of_measure' => fake()->randomElement(['piece', 'kg', 'liter', 'meter']),
            'sku' => fake()->unique()->ean13(),
            'part_number' => fake()->bothify('??-####'),
            'serial_number' => fake()->uuid(),
            'part_condition' => fake()->randomElement(['new', 'used', 'discontinued', 'damaged', 'refurbished']),
            'brand' => fake()->company(),
            'family' => fake()->word(),
            'line' => fake()->word(),
            'is_active' => true,
        ];
    }
}