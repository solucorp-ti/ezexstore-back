<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'logo_url',
        'company_name',
        'company_email',
        'whatsapp_number',
        'search_engine_type',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}