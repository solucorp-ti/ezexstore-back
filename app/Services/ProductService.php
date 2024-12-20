<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService implements ProductServiceInterface
{
    protected $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getProducts(int $tenantId, array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('app.pagination.per_page', 15);
        return $this->repository->getPaginatedForTenant($tenantId, $filters, $perPage);
    }

    public function createProduct(array $data, int $tenantId)
    {
        try {
            DB::beginTransaction();
            
            $data['tenant_id'] = $tenantId;
            $product = $this->repository->create($data);
            
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            throw $e;
        }
    }

    public function findProduct(int $id, int $tenantId)
    {
        return $this->repository->findForTenant($id, $tenantId);
    }

    public function updateProduct(int $id, array $data, int $tenantId)
    {
        try {
            DB::beginTransaction();
            
            $product = $this->repository->findForTenant($id, $tenantId);
            if (!$product) {
                return null;
            }

            $this->repository->update($product, $data);
            
            DB::commit();
            return $product->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProduct(int $id, int $tenantId): bool
    {
        try {
            DB::beginTransaction();
            
            $product = $this->repository->findForTenant($id, $tenantId);
            if (!$product) {
                return false;
            }

            $deleted = $this->repository->delete($product);
            
            DB::commit();
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product: ' . $e->getMessage());
            throw $e;
        }
    }
}