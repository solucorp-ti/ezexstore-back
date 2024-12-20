<?php

namespace App\Services\Interfaces;

interface TenantServiceInterface
{
    public function create(array $data);
    public function update(int $id, array $data);
    public function findBySubdomain(string $subdomain);
}
