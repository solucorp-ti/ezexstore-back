<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ApiKeyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'name' => fake()->bs(),
            'key' => Str::random(32),
            'scopes' => ['products:read', 'products:write'],
            'expires_at' => now()->addYear(),
        ];
    }
}