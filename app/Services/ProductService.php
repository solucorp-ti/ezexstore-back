<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductService
{
    public function __construct(protected ProductRepository $repository) {}

    /**
     * Obtener productos paginados
     */
    public function getPaginatedProducts(int $tenantId, array $filters = []): LengthAwarePaginator 
    {
        return $this->repository->getPaginatedForTenant($tenantId, $filters);
    }

    /**
     * Crear nuevo producto
     */
    public function createProduct(array $data): Product|null
    {
        try {
            DB::beginTransaction();
            
            $product = $this->repository->create($data);
            
            // Si hay fotos, las procesamos aquí
            if (!empty($data['photos'])) {
                // TODO: Implementar lógica de fotos
            }

            DB::commit();
            return $product;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar producto
     */
    public function updateProduct(Product $product, array $data): bool
    {
        try {
            DB::beginTransaction();
            
            $updated = $this->repository->update($product, $data);
            
            if (!empty($data['photos'])) {
                // TODO: Implementar lógica de fotos
            }

            DB::commit();
            return $updated;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar producto
     */
    public function deleteProduct(Product $product): bool
    {
        try {
            DB::beginTransaction();
            
            // Aquí podríamos añadir lógica adicional antes de eliminar
            $deleted = $this->repository->delete($product);
            
            DB::commit();
            return $deleted;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Buscar producto verificando tenant
     */
    public function findProductForTenant(int $productId, int $tenantId): ?Product
    {
        return $this->repository->findForTenant($productId, $tenantId);
    }
}