<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Service;
use App\Models\SparePart;
use App\Models\ActivityLog;
use App\Models\OperationalLog;
use App\Models\PredictionResult;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // First seed the basic data (users, initial customers, etc.)
        $this->call(BigDatabaseSeeder::class);
    }
}
