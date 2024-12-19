<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\InventoryLog\ListInventoryLogRequest;
use Illuminate\Http\JsonResponse;
use App\Services\Interfaces\InventoryLogServiceInterface;

class InventoryLogController extends BaseApiController
{
    protected $inventoryLogService;

    public function __construct(InventoryLogServiceInterface $inventoryLogService)
    {
        $this->inventoryLogService = $inventoryLogService;
    }

    public function index(ListInventoryLogRequest $request): JsonResponse
    {
        $logs = $this->inventoryLogService->getLogs(
            $request->tenant->id,
            $request->validated()
        );

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