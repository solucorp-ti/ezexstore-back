<?php

namespace App\Providers;

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
    InventoryLogServiceInterface
};
use App\Services\{
    ApiKeyService,
    InventoryLogService
};
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Services
        $this->app->bind(ApiKeyServiceInterface::class, ApiKeyService::class);
        $this->app->bind(InventoryLogServiceInterface::class, InventoryLogService::class);

        // Repositories
        $this->app->bind(ApiKeyRepositoryInterface::class, ApiKeyRepository::class);
        $this->app->bind(InventoryLogRepositoryInterface::class, InventoryLogRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
    }
}