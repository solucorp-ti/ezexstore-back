<?php

namespace App\Services;

use App\Exceptions\InventoryException;
use App\Models\Product;
use App\Models\Warehouse;
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
        // Validar ownership
        if (!$this->validateOwnership($productId, $warehouseId, $tenantId)) {
            throw new InventoryException('Invalid product or warehouse for this tenant');
        }

        // Procesar cantidad según tipo
        $adjustedQuantity = $this->processQuantity($productId, $warehouseId, $quantity, $type, $tenantId);

        return DB::transaction(function () use ($productId, $warehouseId, $adjustedQuantity, $type, $tenantId) {
            $this->inventoryRepository->updateStock($productId, $warehouseId, $adjustedQuantity);

            return $this->inventoryRepository->logMovement([
                'tenant_id' => $tenantId,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => $adjustedQuantity,
                'type' => $type
            ]);
        });
    }

    public function getCurrentStock(int $productId, int $warehouseId, int $tenantId)
    {
        if (!$this->validateOwnership($productId, $warehouseId, $tenantId)) {
            throw new InventoryException('Invalid product or warehouse for this tenant');
        }

        return $this->inventoryRepository->getStock($productId, $warehouseId);
    }

    private function validateOwnership(int $productId, int $warehouseId, int $tenantId): bool
    {
        $product = Product::where('id', $productId)
            ->where('tenant_id', $tenantId)
            ->exists();

        $warehouse = Warehouse::where('id', $warehouseId)
            ->where('tenant_id', $tenantId)
            ->exists();

        if (!$product || !$warehouse) {
            throw new InventoryException(
                'Invalid product or warehouse for this tenant',
                InventoryException::INVALID_OWNERSHIP,
                [
                    'product_exists' => $product,
                    'warehouse_exists' => $warehouse
                ]
            );
        }

        return true;
    }

    private function processQuantity(int $productId, int $warehouseId, int $quantity, string $type, int $tenantId): int
    {
        if ($type === 'order') {
            $currentStock = $this->getCurrentStock($productId, $warehouseId, $tenantId);
            $requiredStock = abs($quantity);

            if ($currentStock < $requiredStock) {
                throw new InventoryException(
                    "Insufficient stock. Available: {$currentStock}, Required: {$requiredStock}",
                    InventoryException::INSUFFICIENT_STOCK,
                    [
                        'current_stock' => $currentStock,
                        'required_stock' => $requiredStock,
                        'missing_stock' => $requiredStock - $currentStock
                    ]
                );
            }
            return -$requiredStock;
        }

        return abs($quantity);
    }
}
