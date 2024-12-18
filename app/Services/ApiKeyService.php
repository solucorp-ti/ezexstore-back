<?php

namespace App\Services;

use App\Repositories\Interfaces\ApiKeyRepositoryInterface;
use App\Services\Interfaces\ApiKeyServiceInterface;
use Carbon\Carbon;

class ApiKeyService implements ApiKeyServiceInterface
{
    protected $apiKeyRepository;

    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository)
    {
        $this->apiKeyRepository = $apiKeyRepository;
    }

    public function createApiKey(array $data)
    {
        $data['key'] = $this->apiKeyRepository->generateUniqueKey();
        return $this->apiKeyRepository->create($data);
    }

    public function validateApiKey(string $key)
    {
        $apiKey = $this->apiKeyRepository->findByKey($key);

        if (!$apiKey) {
            return false;
        }

        if ($apiKey->expires_at && Carbon::now()->greaterThan($apiKey->expires_at)) {
            return false;
        }

        $apiKey->update(['last_used_at' => Carbon::now()]);

        return $apiKey;
    }
}
