<?php

namespace App\Repositories;

use App\Models\InventoryLog;
use App\Repositories\Interfaces\InventoryLogRepositoryInterface;

class InventoryLogRepository extends BaseRepository implements InventoryLogRepositoryInterface
{
    protected int $perPage = 15;

    public function __construct(InventoryLog $model)
    {
        parent::__construct($model);
    }

    public function paginateByTenant(int $tenantId, ?int $perPage = null, array $filters = [])
    {
        $query = $this->model
            ->where('tenant_id', $tenantId)
            ->with(['product', 'warehouse']);

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage ?? $this->perPage);
    }
}