<?php

namespace App\Http\Controllers\Api;

use App\Services\Interfaces\InventoryServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\InventoryException;
use Illuminate\Validation\Rule;

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
            'product_id' => [
                'required',
                Rule::exists('products', 'id')->where(function ($query) use ($request) {
                    return $query->where('tenant_id', $request->tenant->id);
                }),
            ],
            'warehouse_id' => [
                'required',
                Rule::exists('warehouses', 'id')->where(function ($query) use ($request) {
                    return $query->where('tenant_id', $request->tenant->id);
                }),
            ],
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:order,restock',
        ]);
    
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }
    
        try {
            $movement = $this->inventoryService->adjustStock(
                $request->product_id,
                $request->warehouse_id,
                $request->quantity,
                $request->type,
                $request->tenant->id
            );
    
            return $this->successResponse($movement, 'Stock adjusted successfully');
        } catch (InventoryException $e) {
            return $this->errorResponse($e->getMessage(), 400, [
                'error_code' => $e->getErrorCode(),
                'details' => $e->getDetails()
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while adjusting stock', 500);
        }
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