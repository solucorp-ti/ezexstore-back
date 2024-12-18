<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('subdomain', 'demo')->first();
        
        if ($tenant) {
            Product::factory()
                ->count(20)
                ->create([
                    'tenant_id' => $tenant->id
                ]);
        }
    }
}