<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InventoryException;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Interfaces\InventoryServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @group Inventory
 *
 * APIs for managing product inventory and stock movements
 */
class InventoryController extends BaseApiController
{
    protected $inventoryService;

    public function __construct(InventoryServiceInterface $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Adjust Stock
     *
     * Adjusts the stock quantity of a product in a specific warehouse.
     * Use type 'restock' to add inventory or 'order' to reduce it.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @bodyParam product_id integer required The ID of the product. Example: 1
     * @bodyParam warehouse_id integer required The ID of the warehouse. Example: 1
     * @bodyParam quantity integer required The quantity to adjust (must be positive). Example: 10
     * @bodyParam type string required The type of adjustment (order, restock). Example: restock
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": {
     *     "product_id": 1,
     *     "warehouse_id": 1,
     *     "quantity": 10,
     *     "type": "restock",
     *     "created_at": "2024-12-18T00:00:00.000000Z"
     *   },
     *   "message": "Stock adjusted successfully"
     * }
     *
     * @response status=400 scenario="insufficient stock" {
     *   "success": false,
     *   "message": "Insufficient stock. Available: 5, Required: 10",
     *   "error_code": "insufficient_stock",
     *   "details": {
     *     "current_stock": 5,
     *     "required_stock": 10,
     *     "missing_stock": 5
     *   }
     * }
     *
     * @response status=400 scenario="invalid ownership" {
     *   "success": false,
     *   "message": "Invalid product or warehouse for this tenant",
     *   "error_code": "invalid_ownership",
     *   "details": {
     *     "product_exists": false,
     *     "warehouse_exists": true
     *   }
     * }
     */
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

    /**
     * Get Current Stock
     *
     * Returns the current stock quantity of a product in a specific warehouse.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @bodyParam product_id integer required The ID of the product. Example: 1
     * @bodyParam warehouse_id integer required The ID of the warehouse. Example: 1
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": {
     *     "stock": 15
     *   },
     *   "message": "Stock retrieved successfully"
     * }
     *
     * @response status=422 scenario="validation error" {
     *   "success": false,
     *   "message": "The product id field is required."
     * }
     */
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
