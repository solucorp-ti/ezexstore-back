<?php

namespace App\Repositories\Interfaces;

interface WarehouseRepositoryInterface extends BaseRepositoryInterface
{
    public function findByTenant(int $tenantId);
}