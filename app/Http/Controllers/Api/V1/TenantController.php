<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tenant\StoreTenantRequest;
use App\Http\Requests\Api\Tenant\UpdateTenantRequest;
use App\Services\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

/**
 * @group Tenants
 *
 * APIs for managing tenant information
 */
class TenantController extends Controller
{
    public function __construct(
        protected TenantService $tenantService
    ) {}

    /**
     * Create Tenant
     *
     * Creates a new tenant with the provided information.
     *
     * @bodyParam name string required The name of the tenant. Example: "Example Tenant"
     * @bodyParam subdomain string required The subdomain of the tenant. Example: "example"
     * @bodyParam config object The configuration of the tenant.
     * @bodyParam user object required The administrator user information.
     * @bodyParam user.name string required The name of the user. Example: "John Doe"
     * @bodyParam user.email string required The email of the user. Example: "john.doe@example.com"
     * @bodyParam user.password string required The password of the user. Example: "password123"
     *
     * @response status=201 scenario="success" {
     *   "message": "Tenant created successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Example Tenant",
     *     "subdomain": "example",
     *     "config": {...},
     *     "users": [...],
     *     "apiKeys": [...]
     *   }
     * }
     *
     * @response status=422 scenario="validation error" {
     *   "success": false,
     *   "message": "Validation error message"
     * }
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        try {
            Log::info('Creating tenant', $request->validated());
            $tenant = $this->tenantService->create($request->validated());

            return response()->json([
                'message' => 'Tenant created successfully',
                'data' => $tenant->load(['config', 'users', 'apiKeys'])
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Tenant
     *
     * Updates the information of an existing tenant.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @urlParam id integer required The ID of the tenant to update. Example: 1
     *
     * @bodyParam name string The updated name of the tenant. Example: "Updated Tenant"
     * @bodyParam subdomain string The updated subdomain of the tenant. Example: "updated"
     * @bodyParam config object The updated configuration of the tenant.
     *
     * @response scenario="success" {
     *   "message": "Tenant updated successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Updated Tenant",
     *     "subdomain": "updated",
     *     "config": {...}
     *   }
     * }
     *
     * @response status=404 scenario="not found" {
     *   "success": false,
     *   "message": "Tenant not found."
     * }
     */
    public function update(UpdateTenantRequest $request, int $id): JsonResponse
    {
        try {
            $tenant = $this->tenantService->update($id, $request->validated());

            return response()->json([
                'message' => 'Tenant updated successfully',
                'data' => $tenant->load(['users', 'warehouses', 'config'])
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}