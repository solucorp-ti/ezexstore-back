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

        return DB::transaction(function () use ($id, $data) {
            return $this->productRepository->update($id, $data);
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
