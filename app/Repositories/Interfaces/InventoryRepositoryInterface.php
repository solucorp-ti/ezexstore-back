<?php

namespace App\Repositories\Interfaces;

interface InventoryRepositoryInterface extends BaseRepositoryInterface
{
    public function updateStock(int $productId, int $warehouseId, int $quantity);
    public function getStock(int $productId, int $warehouseId);
    public function logMovement(array $data);
}