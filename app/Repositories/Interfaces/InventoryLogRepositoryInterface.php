<?php

namespace App\Repositories\Interfaces;

interface InventoryLogRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateByTenant(int $tenantId, ?int $perPage = null, array $filters = []);
}