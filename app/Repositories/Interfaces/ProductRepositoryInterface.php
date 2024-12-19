<?php

namespace App\Repositories\Interfaces;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateByTenant(
        int $tenantId, 
        ?int $perPage = null,
        array $relations = [],
        array $columns = ['*']
    );

    public function findByTenant(int $tenantId);
    
    public function findBySerialAndTenant(string $serial, int $tenantId);
}