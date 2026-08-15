<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 'vehicle_plate', 'vehicle_id', 'type',
        'entry_date', 'completion_date', 'status', 'total_cost'
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'completion_date' => 'datetime',
        'total_cost' => 'decimal:2',
    ];

    public function spareParts(): BelongsToMany
    {
        return $this->belongsToMany(SparePart::class, 'service_spare_part')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeInPeriod($query, $month, $year)
    {
        return $query->whereMonth('entry_date', $month)
                     ->whereYear('entry_date', $year);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function technicians()
    {
        return $this->belongsToMany(User::class, 'service_technician', 'service_id', 'user_id');
    }
}