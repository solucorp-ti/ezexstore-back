<?php

namespace App\Services;

use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Support\Facades\DB;

class ProductService implements ProductServiceInterface
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProducts(int $tenantId)
    {
        return $this->productRepository->findByTenant($tenantId);
    }

    public function findProduct(int $id, int $tenantId)
    {
        $product = $this->productRepository->find($id);

        if (!$product || $product->tenant_id !== $tenantId) {
            return null;
        }

        return $product;
    }

    public function createProduct(array $data, int $tenantId)
    {
        $data['tenant_id'] = $tenantId;
        $data['product_serial'] = $this->productRepository->generateSerial($tenantId);

        return DB::transaction(function () use ($data) {
            return $this->productRepository->create($data);
        });
    }

    public function updateProduct(int $id, array $data, int $tenantId)
    {
        $product = $this->productRepository->find($id);

        if (!$product || $product->tenant_id !== $tenantId) {
            return null;
        }

        return DB::transaction(function () use ($id, $data, $product) {
            // Si se está activando un producto eliminado
            if ((isset($data['status']) && $data['status'] === 'active')) {
                // Primero restauramos el modelo
                $product->restore();
                // Luego actualizamos los demás datos
                $product->update($data);
                // Refrescamos el modelo para asegurarnos que tenemos los datos actualizados
                return $product->fresh();
            }

            return $this->productRepository->update($id, $data);
        });
    }

    public function reactivateProduct(int $id, int $tenantId)
    {
        $product = $this->productRepository->find($id);

        if (!$product || $product->tenant_id !== $tenantId) {
            return false;
        }

        return DB::transaction(function () use ($id) {
            return $this->productRepository->update($id, ['deleted_at' => null, 'status' => 'active']);
        });
    }

    public function deleteProduct(int $id, int $tenantId)
    {
        $product = $this->productRepository->find($id);

        if (!$product || $product->tenant_id !== $tenantId) {
            return false;
        }

        return DB::transaction(function () use ($id) {
            return $this->productRepository->delete($id);
        });
    }
}
