<?php

namespace App\Services;

use App\Repositories\Interfaces\InventoryLogRepositoryInterface;
use App\Services\Interfaces\InventoryLogServiceInterface;

class InventoryLogService implements InventoryLogServiceInterface
{
    public function __construct(
        protected InventoryLogRepositoryInterface $inventoryLogRepository
    ) {}

    public function getLogs(int $tenantId, array $filters = [])
    {
        return $this->inventoryLogRepository->paginateByTenant(
            tenantId: $tenantId,
            perPage: $filters['per_page'] ?? null,
            filters: $filters
        );
    }
}