<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\TenantConfig;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Faker\Generator;
use Illuminate\Container\Container;

class TenantSeeder extends Seeder
{
    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */

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
        $warehouse = Warehouse::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Main Warehouse'
        ]);

        Product::factory()
            ->count(20)
            ->create([
                'tenant_id' => $tenant->id
            ]);

        $products = Product::where('tenant_id', $tenant->id)->get();

        //Añadir inventario a cada producto con el modelo inventorylog
        foreach ($products as $product) {
            InventoryLog::factory()->create([
                'tenant_id' => $tenant->id,
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'quantity' => $this->faker->numberBetween(1, 100),
                'type' => $this->faker->randomElement(['restock', 'order']),
            ]);

            InventoryLog::factory()->create([
                'tenant_id' => $tenant->id,
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'quantity' => $this->faker->numberBetween(1, 100),
                'type' => $this->faker->randomElement(['restock', 'order']),
            ]);

            InventoryLog::factory()->create([
                'tenant_id' => $tenant->id,
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'quantity' => $this->faker->numberBetween(1, 100),
                'type' => $this->faker->randomElement(['restock', 'order']),
            ]);
        }

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
