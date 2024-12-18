<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::factory()
            ->create([
                'name' => 'Demo Store',
                'subdomain' => 'demo'
            ]);

        $user = User::factory()->create([
            'name' => 'Demo Admin',
            'email' => 'admin@demo.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('admin');
        $tenant->users()->attach($user, ['role_id' => 2]); // admin role

        ApiKey::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'name' => 'Demo API Key'
        ]);

        Warehouse::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Main Warehouse'
        ]);
    }
}