<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function find($id)
    {
        return $this->model->withTrashed()->find($id);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
        }
        return $record;
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

    public function generateSerial(int $tenantId): string
    {
        $lastProduct = $this->model
            ->where('tenant_id', $tenantId)
            ->latest('id')
            ->first();

        $sequence = $lastProduct ? (int)substr($lastProduct->product_serial, -6) + 1 : 1;
        return sprintf('P%06d', $sequence);
    }

    public function delete($id)
    {
        $record = $this->find($id);
        if ($record) {
            return DB::transaction(function () use ($record) {
                $record->update(['status' => 'inactive']);
                $record->delete();
                return $record;
            });
        }
        return $record;
    }
}
