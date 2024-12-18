<?php

namespace App\Services\Interfaces;

interface InventoryServiceInterface
{
    public function adjustStock(int $productId, int $warehouseId, int $quantity, string $type, int $tenantId);
    public function getCurrentStock(int $productId, int $warehouseId, int $tenantId);
}