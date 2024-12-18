<?php

namespace App\Providers;

use App\Repositories\ApiKeyRepository;
use App\Repositories\Interfaces\ApiKeyRepositoryInterface;
use App\Repositories\Interfaces\TenantRepositoryInterface;
use App\Repositories\TenantRepository;
use App\Services\ApiKeyService;
use App\Services\Interfaces\ApiKeyServiceInterface;
use Illuminate\Support\ServiceProvider;


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
        $this->app->bind(ApiKeyServiceInterface::class, ApiKeyService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
