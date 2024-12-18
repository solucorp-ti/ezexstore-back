<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use Illuminate\Http\Request;

class WarehouseController extends BaseApiController
{
    protected $warehouseRepository;

    public function __construct(WarehouseRepositoryInterface $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    public function index(Request $request)
    {
        $warehouses = $this->warehouseRepository->findByTenant($request->tenant->id);
        return $this->successResponse($warehouses, 'Warehouses retrieved successfully');
    }
}