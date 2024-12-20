<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestTenantSeeder extends Seeder
{
    public function run(): void
    {
        // Crear tenant de prueba
        $tenant = Tenant::create([
            'name' => 'Test Store',
            'subdomain' => 'test'
        ]);

        // Crear usuario admin
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        // Asignar rol admin
        $user->assignRole('admin');

        // Vincular usuario al tenant
        $tenant->users()->attach($user, ['role_id' => 2]); // 2 = admin

        // Crear API key
        ApiKey::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'name' => 'Test API Key',
            'key' => Str::random(32),
            'scopes' => ['products:read', 'products:write'],
        ]);
    }
}

