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

    public function findByIdentifier(string $serial, ?string $sku, int $tenantId)
    {
        // Primero buscamos por serial ya que es el identificador principal
        $product = $this->repository->findBySerialForTenant($serial, $tenantId);

        // Si no encontramos por serial y tenemos SKU, buscamos por SKU
        if (!$product && $sku) {
            $product = $this->repository->findBySkuForTenant($sku, $tenantId);
        }

        return $product;
    }

    public function syncProduct(array $data, int $tenantId)
    {
        try {
            DB::beginTransaction();

            $product = $this->findByIdentifier(
                $data['product_serial'],
                $data['sku'] ?? null,
                $tenantId
            );

            if ($product) {
                // Actualizamos el producto existente
                $this->repository->update($product, $data);
                $result = $product->fresh();
                $isNew = false;
            } else {
                // Creamos un nuevo producto
                $data['tenant_id'] = $tenantId;
                $result = $this->repository->create($data);
                $isNew = true;
            }

            DB::commit();

            return [
                'product' => $result,
                'is_new' => $isNew
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncing product: ' . $e->getMessage(), [
                'product_serial' => $data['product_serial'] ?? null,
                'sku' => $data['sku'] ?? null,
                'tenant_id' => $tenantId
            ]);
            throw $e;
        }
    }
}
