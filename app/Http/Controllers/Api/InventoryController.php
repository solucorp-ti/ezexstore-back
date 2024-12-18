<?php

namespace App\Http\Controllers\Api;

use App\Services\Interfaces\InventoryServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends BaseApiController
{
    protected $inventoryService;

    public function __construct(InventoryServiceInterface $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function adjustStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer',
            'type' => 'required|in:order,restock',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $movement = $this->inventoryService->adjustStock(
            $request->product_id,
            $request->warehouse_id,
            $request->quantity,
            $request->type,
            $request->tenant->id
        );

        return $this->successResponse($movement, 'Stock adjusted successfully');
    }

    public function getStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $stock = $this->inventoryService->getCurrentStock(
            $request->product_id,
            $request->warehouse_id,
            $request->tenant->id
        );

        return $this->successResponse(['stock' => $stock], 'Stock retrieved successfully');
    }
}