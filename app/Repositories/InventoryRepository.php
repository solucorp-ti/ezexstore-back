<?php

namespace App\Repositories;

use App\Models\InventoryLog;
use App\Models\Product;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    protected $inventoryLog;

    public function __construct(Product $model, InventoryLog $inventoryLog)
    {
        parent::__construct($model);
        $this->inventoryLog = $inventoryLog;
    }

    public function updateStock(int $productId, int $warehouseId, int $quantity)
    {
        return DB::table('product_warehouse')
            ->updateOrInsert(
                ['product_id' => $productId, 'warehouse_id' => $warehouseId],
                ['stock' => DB::raw("stock + $quantity")]
            );
    }

    public function getStock(int $productId, int $warehouseId)
    {
        return DB::table('product_warehouse')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->value('stock') ?? 0;
    }

    public function logMovement(array $data)
    {
        return $this->inventoryLog->create($data);
    }
}
