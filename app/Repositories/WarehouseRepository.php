<?php

namespace App\Repositories;

use App\Models\Warehouse;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;

class WarehouseRepository extends BaseRepository implements WarehouseRepositoryInterface
{
    public function __construct(Warehouse $model)
    {
        parent::__construct($model);
    }

    public function findByTenant(int $tenantId)
    {
        return $this->model->where('tenant_id', $tenantId)->get();
    }
}