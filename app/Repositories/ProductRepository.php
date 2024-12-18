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
