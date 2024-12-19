<?php

namespace App\Services;

use App\Repositories\TenantRepository;
use Illuminate\Support\Str;

class TenantService
{
    public function __construct(
        protected TenantRepository $tenantRepository
    ) {}

    public function create(array $data)
    {
        // Aseguramos que el subdominio sea único y válido
        $data['subdomain'] = Str::slug($data['subdomain']);
        
        return $this->tenantRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        if (isset($data['subdomain'])) {
            $data['subdomain'] = Str::slug($data['subdomain']);
        }
        
        return $this->tenantRepository->update($id, $data);
    }
}