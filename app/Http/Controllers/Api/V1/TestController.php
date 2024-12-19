<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;

/**
 * @group Connection Test
 *
 * APIs for testing API connection and authentication
 */
class TestController extends BaseApiController
{
    /**
     * Test API Connection
     *
     * Returns basic information about the authenticated tenant and user to verify API connectivity.
     *
     * @header X-API-KEY required The API key for authentication
     *
     * @response scenario="success" {
     *   "success": true,
     *   "data": {
     *     "tenant": {
     *       "id": 1,
     *       "name": "Demo Store",
     *       "subdomain": "demo"
     *     },
     *     "user": {
     *       "id": 1,
     *       "name": "Demo Admin",
     *       "email": "admin@demo.com"
     *     },
     *     "scopes": ["products:read", "products:write", "inventory:read", "inventory:write"]
     *   },
     *   "message": "API connection successful"
     * }
     *
     * @response status=401 scenario="invalid api key" {
     *   "success": false,
     *   "message": "Invalid API key"
     * }
     */
    public function testConnection(Request $request)
    {
        $tenant = $request->tenant;
        $user = $request->api_user;

        return $this->successResponse([
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'subdomain' => $tenant->subdomain,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'scopes' => $request->api_key->scopes ?? [],
        ], 'API connection successful');
    }
}
