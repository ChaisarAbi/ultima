<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_date',
        'total_services',
        'completed_services',
        'avg_completion_hours',
        'total_spare_used',
        'total_revenue'
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    /**
     * Scope for a specific month/year.
     */
    public function scopeInMonth($query, $month, $year)
    {
        return $query->whereMonth('log_date', $month)
                     ->whereYear('log_date', $year);
    }

    /**
     * Scope for the last N days.
     */
    public function scopeLastDays($query, $days)
    {
        return $query->where('log_date', '>=', now()->subDays($days));
    }
}
