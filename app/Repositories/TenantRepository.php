<?php

namespace App\Repositories;

use App\Models\Tenant;
use App\Repositories\Interfaces\TenantRepositoryInterface;

class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    public function __construct(Tenant $model)
    {
        parent::__construct($model);
    }

    public function findBySubdomain(string $subdomain)
    {
        return $this->model->where('subdomain', $subdomain)->first();
    }
}
