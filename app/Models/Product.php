<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'product_serial',
        'product_name',
        'description',
        'base_price',
        'status',
        'unit_of_measure',
        'sku',
        'part_number',
        'serial_number',
        'part_condition',
        'brand',
        'family',
        'line',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class)
            ->withPivot('stock')
            ->withTimestamps();
    }

    public function photos()
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    /**
     * Scope para filtrar por tenant actual
     */
    public function scopeForTenant(Builder $query, $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope para productos activos
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('status', 'active');
    }

    /**
     * Scope para búsqueda general
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('product_name', 'like', "%{$search}%")
                ->orWhere('product_serial', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('part_number', 'like', "%{$search}%");
        });
    }

    /**
     * Scope para filtrar por condición
     */
    public function scopeByCondition(Builder $query, string $condition): Builder
    {
        return $query->where('part_condition', $condition);
    }

    /**
     * Scope para filtrar por rango de precio
     */
    public function scopePriceRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('base_price', [$min, $max]);
    }

    /**
     * Scope para productos con stock en algún almacén
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereHas('warehouses', function ($query) {
            $query->where('stock', '>', 0);
        });
    }

    /**
     * Scope para productos sin stock
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->whereDoesntHave('warehouses', function ($query) {
            $query->where('stock', '>', 0);
        });
    }
}
