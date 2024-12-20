<?php

namespace App\Providers;

use App\Repositories\ApiKeyRepository;
use App\Repositories\Interfaces\ApiKeyRepositoryInterface;
use App\Repositories\Interfaces\TenantRepositoryInterface;
use App\Repositories\TenantRepository;
use App\Services\ApiKeyService;
use App\Services\Interfaces\ApiKeyServiceInterface;
use Illuminate\Support\ServiceProvider;

use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\ProductService;

use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Repositories\InventoryRepository;
use App\Services\Interfaces\InventoryServiceInterface;
use App\Services\InventoryService;

use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use App\Repositories\WarehouseRepository;

use App\Services\TenantService;
use App\Services\Interfaces\TenantServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(ApiKeyRepositoryInterface::class, ApiKeyRepository::class);
        $this->app->bind(ApiKeyServiceInterface::class, ApiKeyService::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(InventoryServiceInterface::class, InventoryService::class);
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(TenantServiceInterface::class, TenantService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
