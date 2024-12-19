<?php

namespace App\Services;

use App\Repositories\TenantRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Log;

class TenantService
{
    public function __construct(
        protected TenantRepository $tenantRepository,
        protected UserRepository $userRepository
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

            // Preparar datos del usuario
            $userData = [
                'name' => $data['user']['name'],
                'email' => $data['user']['email'],
                'password' => bcrypt($data['user']['password'])
            ];

            // Crear el usuario
            $user = $this->userRepository->create($userData);
            Log::info('User created', $user->toArray());
            
            // Asociar el usuario con el tenant
            $tenant->users()->attach($user->id, ['role_id' => $data['user']['role_id'] ?? null]);

            // Crear API key
            $apiKeyData = [
                'key' => Str::random(40),
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'scopes' => ['products:read', 'products:write', 'inventory:read', 'inventory:write'],
                'name' => 'Default API Key',
            ];
            $apiKey = $tenant->apiKeys()->create($apiKeyData);
            Log::info('API Key created', $apiKey->toArray());
            
            // Cargar la relación config, users y apiKeys
            $tenant->load('config', 'users', 'apiKeys');
            
            return $tenant;
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            
            $tenant = $this->tenantRepository->update($id, $data);
            
            if (isset($data['config'])) {
                $tenant->config()->update($data['config']);
                $tenant->load('config');
            }
            
            return $tenant;
        });
    }
}