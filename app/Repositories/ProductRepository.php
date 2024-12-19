<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function __construct(protected Product $model) {}

    /**
     * Obtener productos paginados por tenant
     */
    public function getPaginatedForTenant(
        int $tenantId, 
        array $filters = [], 
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = $this->model->forTenant($tenantId);
        
        return $this->applyFilters($query, $filters)->paginate($perPage);
    }

    /**
     * Crear nuevo producto
     */
    public function create(array $data): Product 
    {
        return $this->model->create($data);
    }

    /**
     * Actualizar producto existente
     */
    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    /**
     * Eliminar producto
     */
    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    /**
     * Buscar producto por ID verificando tenant
     */
    public function findForTenant(int $productId, int $tenantId): ?Product
    {
        return $this->model->forTenant($tenantId)->find($productId);
    }

    /**
     * Aplicar filtros a la consulta
     */
    protected function applyFilters($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['condition'])) {
            $query->byCondition($filters['condition']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('base_price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('base_price', '<=', $filters['max_price']);
        }

        if (isset($filters['in_stock']) && $filters['in_stock']) {
            $query->inStock();
        }

        if (isset($filters['active'])) {
            $query->where('is_active', $filters['active']);
        }

        return $query;
    }
}