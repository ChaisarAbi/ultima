<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SparePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'part_number', 'stock', 'minimum_stock', 'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Services that use this spare part.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_spare_part')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    /**
     * Check if stock is low.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }

    /**
     * Scope low stock parts.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'minimum_stock');
    }
}
