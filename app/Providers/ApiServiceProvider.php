<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Repositories\Interfaces\{
    ApiKeyRepositoryInterface,
    InventoryLogRepositoryInterface,
    ProductRepositoryInterface,
    TenantRepositoryInterface,
    UserRepositoryInterface,
    WarehouseRepositoryInterface
};
use App\Repositories\{
    ApiKeyRepository,
    InventoryLogRepository,
    ProductRepository,
    TenantRepository,
    UserRepository,
    WarehouseRepository
};
use App\Services\Interfaces\{
    ApiKeyServiceInterface,
    InventoryLogServiceInterface,
    ProductServiceInterface,
    ProductPhotoServiceInterface,
    TenantServiceInterface
};
use App\Services\{
    ApiKeyService,
    InventoryLogService,
    ProductService,
    ProductPhotoService,
    TenantService
};
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Services
        $this->app->bind(ApiKeyServiceInterface::class, ApiKeyService::class);
        $this->app->bind(InventoryLogServiceInterface::class, InventoryLogService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(TenantServiceInterface::class, TenantService::class);

        // Repositories
        $this->app->bind(ApiKeyRepositoryInterface::class, ApiKeyRepository::class);
        $this->app->bind(InventoryLogRepositoryInterface::class, InventoryLogRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(ProductRepository::class, function ($app) {
            return new ProductRepository($app->make(\App\Models\Product::class));
        });
        $this->app->bind(ProductPhotoServiceInterface::class, ProductPhotoService::class);
    }
}