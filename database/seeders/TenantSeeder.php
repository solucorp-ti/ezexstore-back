<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\Tenant;
use App\Models\TenantConfig;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un Tenant
        $tenant = Tenant::factory()->create([
            'name' => 'Demo Store',
            'subdomain' => 'demo'
        ]);

        // Crear un Usuario y asignar rol
        $user = User::factory()->create([
            'name' => 'Demo Admin',
            'email' => 'admin@demo.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('admin');
        $tenant->users()->attach($user, ['role_id' => 2]); // Rol admin

        // Crear una API Key
        ApiKey::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'name' => 'Demo API Key',
            'scopes' => ['products:read', 'products:write', 'inventory:read', 'inventory:write']
        ]);

        // Crear un Almacén (Warehouse)
        Warehouse::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Main Warehouse'
        ]);

        TenantConfig::create([
            'tenant_id' => $tenant->id,
            'logo_url' => 'https://example.com/logo.png',
            'company_name' => 'Demo Company',
            'company_email' => 'contact@demo.com',
            'whatsapp_number' => '+123456789',
            'search_engine_type' => 'regular',
        ]);
    }
}
