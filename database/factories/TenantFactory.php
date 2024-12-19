<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'subdomain' => Str::slug($this->faker->unique()->word),
        ];
    }

    public function withConfig(): self
    {
        return $this->afterCreating(function ($tenant) {
            $tenant->config()->create([
                'company_name' => $this->faker->company,
                'company_email' => $this->faker->companyEmail,
                'search_engine_type' => $this->faker->randomElement(['regular', 'expandable'])
            ]);
        });
    }
}