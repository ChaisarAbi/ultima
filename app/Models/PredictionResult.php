<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredictionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_date', 'metric', 'predicted_value', 'actual_value', 'model_version', 'generated_at'
    ];

    protected $casts = [
        'target_date' => 'date',
        'generated_at' => 'datetime',
    ];
}