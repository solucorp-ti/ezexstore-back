<?php

namespace App\Http\Middleware;

use App\Services\Interfaces\ApiKeyServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiKey;

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
    
        // Verificar scope según el endpoint y método
        $scope = $this->getRequiredScope($request);
        if (!$this->hasValidScope($validKey, $scope)) {
            return response()->json([
                'message' => 'Insufficient permissions'
            ], 403);
        }
    
        $request->merge([
            'tenant' => $validKey->tenant,
            'api_user' => $validKey->user,
            'api_key' => $validKey
        ]);
    
        return $next($request);
    }
    
    private function getRequiredScope(Request $request): string
    {
        $path = $request->path();
        $method = $request->method();
    
        if (str_contains($path, 'products')) {
            return $method === 'GET' ? 'products:read' : 'products:write';
        }
    
        if (str_contains($path, 'inventory')) {
            return $method === 'GET' ? 'inventory:read' : 'inventory:write';
        }
    
        return '';
    }
    
    private function hasValidScope(ApiKey $apiKey, string $requiredScope): bool
    {
        return empty($requiredScope) || 
               in_array($requiredScope, $apiKey->scopes ?? []) ||
               in_array('*', $apiKey->scopes ?? []);
    }
}
