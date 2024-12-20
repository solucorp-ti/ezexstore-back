<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'photo_url',
    ];

    protected $appends = ['full_url'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get full URL for photo
     */
    public function getFullUrlAttribute(): string
    {
        return asset('storage/' . $this->photo_url);
    }
}