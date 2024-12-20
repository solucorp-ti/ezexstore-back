<?php

namespace App\Services\Interfaces;

interface ProductServiceInterface
{
    /**
     * Get filtered products for a tenant
     */
    public function getProducts(int $tenantId, array $filters = [], ?int $perPage = null);

    /**
     * Create a new product
     */
    public function createProduct(array $data, int $tenantId);

    /**
     * Find a product by ID for a tenant
     */
    public function findProduct(int $id, int $tenantId);

    /**
     * Update a product
     */
    public function updateProduct(int $id, array $data, int $tenantId);

    /**
     * Delete a product
     */
    public function deleteProduct(int $id, int $tenantId): bool;
}