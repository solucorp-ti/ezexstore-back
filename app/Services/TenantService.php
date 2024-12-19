<?php

namespace App\Services;

use App\Repositories\TenantRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantService
{
    public function __construct(
        protected TenantRepository $tenantRepository
    ) {}

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Preparar datos del tenant
            $tenantData = [
                'name' => $data['name'],
                'subdomain' => Str::slug($data['subdomain'])
            ];
            
            // Crear tenant
            $tenant = $this->tenantRepository->create($tenantData);
            
            // Crear configuración
            $tenant->config()->create([
                'logo_url' => $data['config']['logo_url'] ?? null,
                'company_name' => $data['config']['company_name'] ?? null,
                'company_email' => $data['config']['company_email'] ?? null,
                'whatsapp_number' => $data['config']['whatsapp_number'] ?? null,
                'search_engine_type' => $data['config']['search_engine_type'],
            ]);
            
            // Cargar la relación config
            $tenant->load('config');
            
            return $tenant;
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            if (isset($data['subdomain'])) {
                $data['subdomain'] = Str::slug($data['subdomain']);
            }
            
            $tenant = $this->tenantRepository->update($id, $data);
            
            if (isset($data['config'])) {
                $tenant->config()->update($data['config']);
                $tenant->load('config');
            }
            
            return $tenant;
        });
    }
}