<?php

namespace App\Repositories\Interfaces;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function findByTenant(int $tenantId);
    public function findBySerialAndTenant(string $serial, int $tenantId);
}