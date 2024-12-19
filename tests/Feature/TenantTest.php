<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    public function test_can_create_tenant_with_config(): void
    {
        $tenantData = [
            'name' => 'Test Company',
            'subdomain' => 'test-company',
            'config' => [
                'company_name' => 'Test Company Legal Name',
                'company_email' => 'contact@testcompany.com',
                'search_engine_type' => 'regular'
            ]
        ];

        $response = $this->postJson('/api/v1/tenants', $tenantData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Tenant created successfully'
            ]);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Test Company',
            'subdomain' => 'test-company'
        ]);

        $this->assertDatabaseHas('tenant_configs', [
            'company_name' => 'Test Company Legal Name',
            'company_email' => 'contact@testcompany.com'
        ]);
    }

    public function test_cannot_create_tenant_with_duplicate_subdomain(): void
    {
        Tenant::factory()->create(['subdomain' => 'test-company']);

        $tenantData = [
            'name' => 'Another Company',
            'subdomain' => 'test-company',
            'config' => [
                'search_engine_type' => 'regular'
            ]
        ];

        $response = $this->postJson('/api/v1/tenants', $tenantData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subdomain']);
    }
}