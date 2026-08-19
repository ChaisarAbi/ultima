<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'base_price',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'base_price' => 'decimal:0',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'type', 'slug');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}