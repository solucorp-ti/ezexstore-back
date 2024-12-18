<?php

namespace App\Services\Interfaces;

interface ProductServiceInterface 
{
    public function getProducts(int $tenantId);
    public function findProduct(int $id, int $tenantId);
    public function createProduct(array $data, int $tenantId);
    public function updateProduct(int $id, array $data, int $tenantId);
    public function deleteProduct(int $id, int $tenantId);
}