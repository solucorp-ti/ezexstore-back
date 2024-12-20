<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function getPaginatedForTenant(
        int $tenantId, 
        array $filters = [], 
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('product_name', 'like', "%{$search}%")
                        ->orWhere('product_serial', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('part_number', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function (Builder $query, string $status) {
                $query->where('status', $status);
            })
            ->when($filters['condition'] ?? null, function (Builder $query, string $condition) {
                $query->where('part_condition', $condition);
            })
            ->when($filters['min_price'] ?? null, function (Builder $query, $price) {
                $query->where('base_price', '>=', $price);
            })
            ->when($filters['max_price'] ?? null, function (Builder $query, $price) {
                $query->where('base_price', '<=', $price);
            })
            ->when($filters['in_stock'] ?? null, function (Builder $query) {
                $query->whereHas('warehouses', function ($query) {
                    $query->where('stock', '>', 0);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function findForTenant(int $productId, int $tenantId): ?Product
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->find($productId);
    }

    public function findBySkuForTenant(string $sku, int $tenantId): ?Product
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('sku', $sku)
            ->first();
    }
}