<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\InventoryLog\ListInventoryLogRequest;
use App\Services\Interfaces\InventoryLogServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * @group Inventory Logs
 *
 * APIs for managing and retrieving inventory logs
 */
class InventoryLogController extends BaseApiController
{
    protected InventoryLogServiceInterface $inventoryLogService;

    public function __construct(InventoryLogServiceInterface $inventoryLogService)
    {
        $this->inventoryLogService = $inventoryLogService;
    }

    /**
     * List Inventory Logs
     *
     * Retrieves a paginated list of inventory logs for a specific tenant.
     * These logs include changes in inventory, restocks, orders, and other related events.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @queryParam start_date string The start date for filtering logs. Format: Y-m-d. Example: 2024-01-01
     * @queryParam end_date string The end date for filtering logs. Format: Y-m-d. Example: 2024-12-31
     * @queryParam event_type string Filter logs by event type. Example: restock
     * @queryParam page integer The page number for pagination. Example: 1
     * @queryParam per_page integer The number of logs per page. Default: 15. Example: 10
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "tenant_id": 1,
     *       "product_id": 1,
     *       "warehouse_id": 1,
     *       "event_type": "restock",
     *       "quantity": 10,
     *       "created_at": "2024-12-18T00:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 3,
     *     "per_page": 15,
     *     "total": 45
     *   },
     *   "message": "Inventory logs retrieved successfully"
     * }
     *
     * @response status=422 scenario="validation error" {
     *   "success": false,
     *   "message": "The start_date field must be a valid date."
     * }
     */
    public function index(ListInventoryLogRequest $request): JsonResponse
    {
        Log::info('Entrando al método index del controlador InventoryLogController');
        Log::info('Tenant ID:', ['tenant' => $request->tenant->id]);
        Log::info('Filtros recibidos:', $request->validated());

        $logs = $this->inventoryLogService->getLogs(
            $request->tenant->id,
            $request->validated()
        );

        Log::info('Logs recuperados:', $logs->toArray());

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total()
            ],
            'message' => 'Inventory logs retrieved successfully'
        ]);
    }
}
