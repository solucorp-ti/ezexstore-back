<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}