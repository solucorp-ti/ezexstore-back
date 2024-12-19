<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tenant\StoreTenantRequest;
use App\Http\Requests\Api\Tenant\UpdateTenantRequest;
use App\Services\TenantService;
use Illuminate\Http\JsonResponse;

class TenantController extends Controller
{
    public function __construct(
        protected TenantService $tenantService
    ) {}

    public function store(StoreTenantRequest $request): JsonResponse
    {
        $tenant = $this->tenantService->create($request->validated());
        
        return response()->json([
            'message' => 'Tenant created successfully',
            'data' => $tenant
        ], 201);
    }

    public function update(UpdateTenantRequest $request, int $id): JsonResponse
    {
        $tenant = $this->tenantService->update($id, $request->validated());
        
        return response()->json([
            'message' => 'Tenant updated successfully',
            'data' => $tenant
        ]);
    }
}