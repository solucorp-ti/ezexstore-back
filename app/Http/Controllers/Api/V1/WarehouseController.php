<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use Illuminate\Http\Request;

/**
 * @group Warehouses
 *
 * APIs for managing tenant warehouses
 */
class WarehouseController extends BaseApiController
{
    protected $warehouseRepository;

    public function __construct(WarehouseRepositoryInterface $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * List Warehouses
     *
     * Returns a list of all warehouses belonging to the authenticated tenant.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "tenant_id": 1,
     *       "name": "Main Warehouse",
     *       "address": "123 Storage St.",
     *       "created_at": "2024-12-18T00:00:00.000000Z",
     *       "updated_at": "2024-12-18T00:00:00.000000Z"
     *     }
     *   ],
     *   "message": "Warehouses retrieved successfully"
     * }
     *
     * @response status=401 scenario="unauthorized" {
     *   "success": false,
     *   "message": "Invalid API key"
     * }
     */
    public function index(Request $request)
    {
        $warehouses = $this->warehouseRepository->findByTenant($request->tenant->id);
        return $this->successResponse($warehouses, 'Warehouses retrieved successfully');
    }
}
