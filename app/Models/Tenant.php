<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subdomain',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_users')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function config()
    {
        return $this->hasOne(TenantConfig::class);
    }

    public function supportAssignments()
    {
        return $this->hasMany(SupportAssignment::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }
}
