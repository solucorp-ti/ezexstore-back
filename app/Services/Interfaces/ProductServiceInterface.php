<?php

namespace App\Services\Interfaces;

interface ProductServiceInterface
{
    public function getProducts(int $tenantId, array $filters = [], ?int $perPage = null);
    public function createProduct(array $data, int $tenantId);
    public function findProduct(int $id, int $tenantId);
    public function updateProduct(int $id, array $data, int $tenantId);
    public function deleteProduct(int $id, int $tenantId): bool;

    public function findByIdentifier(string $serial, ?string $sku, int $tenantId);
    public function syncProduct(array $data, int $tenantId);
}