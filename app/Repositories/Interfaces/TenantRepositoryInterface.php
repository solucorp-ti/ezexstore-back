<?php

namespace App\Repositories\Interfaces;

interface TenantRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySubdomain(string $subdomain);
}
