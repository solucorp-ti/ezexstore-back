<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class TestController extends BaseApiController
{
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
