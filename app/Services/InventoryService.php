<?php

namespace App\Services;

use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Services\Interfaces\InventoryServiceInterface;
use Illuminate\Support\Facades\DB;

class InventoryService implements InventoryServiceInterface
{
    protected $inventoryRepository;

    public function __construct(InventoryRepositoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function adjustStock(int $productId, int $warehouseId, int $quantity, string $type, int $tenantId)
    {
        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $type, $tenantId) {
            $this->inventoryRepository->updateStock($productId, $warehouseId, $quantity);

            return $this->inventoryRepository->logMovement([
                'tenant_id' => $tenantId,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'type' => $type
            ]);
        });
    }

    public function getCurrentStock(int $productId, int $warehouseId, int $tenantId)
    {
        return $this->inventoryRepository->getStock($productId, $warehouseId);
    }
}
