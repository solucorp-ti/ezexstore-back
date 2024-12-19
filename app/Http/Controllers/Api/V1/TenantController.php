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
     * Creates a new tenant with the provided information. This is a public endpoint.
     * 
     * No authentication required.
     *
     * @unauthenticated
     * @group Tenants
     *
     * @bodyParam name string required The name of the tenant. Example: "Example Tenant"
     * @bodyParam subdomain string required The subdomain of the tenant (only lowercase alphanumeric and hyphens). Example: "example"
     * @bodyParam config object required The configuration of the tenant.
     * @bodyParam config.company_name string The company name. Example: "Example Company"
     * @bodyParam config.company_email string Email of the company. Example: "contact@example.com" 
     * @bodyParam config.search_engine_type string required The type of search engine (regular/expandable). Example: "regular"
     * @bodyParam user object required The administrator user information.
     * @bodyParam user.name string required The name of the user. Example: "John Doe"
     * @bodyParam user.email string required The email of the user. Example: "john.doe@example.com"
     * @bodyParam user.password string required The password of the user (min 8 characters). Example: "password123"
     * @bodyParam user.role_id integer required The role ID for the user. Example: 2
     *
     * @response status=201 scenario="success" {
     *   "message": "Tenant created successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Example Tenant",
     *     "subdomain": "example", 
     *     "config": {
     *       "company_name": "Example Company",
     *       "company_email": "contact@example.com",
     *       "search_engine_type": "regular"
     *     },
     *     "users": [{
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "john.doe@example.com",
     *       "pivot": {
     *         "role_id": 2
     *       }
     *     }],
     *     "apiKeys": [{
     *       "id": 1,
     *       "key": "api-key-string",
     *       "scopes": ["products:read","products:write","inventory:read","inventory:write"]
     *     }]
     *   }
     * }
     *
     * @response status=422 scenario="validation error" {
     *   "message": "Validation error",
     *   "errors": {
     *     "subdomain": ["Este subdominio ya está en uso."],
     *     "user.email": ["Este correo electrónico ya está registrado."],
     *     "user.role_id": ["El rol es requerido."]
     *   }
     * }
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        try {
            // El request se valida automáticamente porque usamos StoreTenantRequest
            Log::info('Tenant creation request data:', $request->validated());
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
     * Updates the information of the current tenant. The tenant is identified by the API key.
     *
     * @authenticated
     * @group Tenants
     *
     * @header X-API-KEY required The API key identifies the tenant to update
     *
     * @bodyParam name string The updated name of the tenant. Example: "Updated Tenant"
     * @bodyParam config object The updated configuration of the tenant.
     * @bodyParam config.company_name string The company name. Example: "Updated Company"
     * @bodyParam config.company_email string Email of the company. Example: "new@example.com"
     * @bodyParam config.whatsapp_number string WhatsApp number. Example: "+1234567890"
     * @bodyParam config.search_engine_type string The type of search engine (regular/expandable). Example: "expandable"
     *
     * @response scenario="success" {
     *   "message": "Tenant updated successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Updated Tenant",
     *     "config": {
     *       "company_name": "Updated Company",
     *       "company_email": "new@example.com",
     *       "whatsapp_number": "+1234567890",
     *       "search_engine_type": "expandable"
     *     },
     *     "users": [...],
     *     "warehouses": [...]
     *   }
     * }
     *
     * @response status=422 scenario="validation error" {
     *   "message": "Validation error",
     *   "errors": {
     *     "name": ["El nombre es requerido."],
     *     "config.search_engine_type": ["El tipo de buscador debe ser regular o expandable."]
     *   }
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
