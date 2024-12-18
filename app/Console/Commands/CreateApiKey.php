<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateApiKey extends Command
{
    protected $signature = 'api-key:create {tenant_id} {user_id} {--name=} {--scopes=*}';
    protected $description = 'Create a new API key';

    public function handle()
    {
        $apiKey = ApiKey::create([
            'tenant_id' => $this->argument('tenant_id'),
            'user_id' => $this->argument('user_id'),
            'name' => $this->option('name') ?? 'API Key ' . now(),
            'key' => Str::random(32),
            'scopes' => $this->option('scopes') ?: ['products:read', 'products:write', 'inventory:read', 'inventory:write']
        ]);

        $this->info('API Key created successfully!');
        $this->info('Key: ' . $apiKey->key);
        $this->info('Scopes: ' . implode(', ', $apiKey->scopes));
    }
}