<?php

namespace App\Http\Middleware;

use App\Services\Interfaces\ApiKeyServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    protected $apiKeyService;

    public function __construct(ApiKeyServiceInterface $apiKeyService)
    {
        $this->apiKeyService = $apiKeyService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey) {
            return response()->json([
                'message' => 'API key is missing'
            ], 401);
        }

        $validKey = $this->apiKeyService->validateApiKey($apiKey);

        if (!$validKey) {
            return response()->json([
                'message' => 'Invalid API key'
            ], 401);
        }

        // Agregar tenant y usuario al request para uso posterior
        $request->merge([
            'tenant' => $validKey->tenant,
            'api_user' => $validKey->user
        ]);

        return $next($request);
    }
}
