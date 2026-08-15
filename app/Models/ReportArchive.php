<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'type', 'report_date', 'file_path', 'report_data', 'period', 'month', 'year'
    ];

    protected $casts = [
        'report_date' => 'datetime',
    ];
}