<?php

namespace App\Services\Interfaces;

interface ApiKeyServiceInterface
{
    public function createApiKey(array $data);
    public function validateApiKey(string $key);
}
