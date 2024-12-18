<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function findByTenant(int $tenantId)
    {
        return $this->model->where('tenant_id', $tenantId)->get();
    }

    public function findBySerialAndTenant(string $serial, int $tenantId)
    {
        return $this->model->where('tenant_id', $tenantId)
            ->where('product_serial', $serial)
            ->first();
    }

    public function create(array $data)
    {
        $data['product_serial'] = $this->generateSerial($data['tenant_id']);
        return parent::create($data);
    }

    private function generateSerial(int $tenantId): string
    {
        $lastProduct = $this->model
            ->where('tenant_id', $tenantId)
            ->latest('id')
            ->first();

        $sequence = $lastProduct ? (int)substr($lastProduct->product_serial, -6) + 1 : 1;
        return sprintf('P%06d', $sequence);
    }
}
