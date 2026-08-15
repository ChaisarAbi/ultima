<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0');
DB::table('customers')->truncate();
DB::table('vehicles')->truncate();
DB::table('services')->truncate();
DB::table('service_spare_part')->truncate();
DB::table('service_technician')->truncate();
DB::table('spare_parts')->truncate();
DB::table('operational_logs')->truncate();
DB::table('prediction_results')->truncate();
DB::table('activity_logs')->truncate();
DB::table('report_archives')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1');
echo "All truncated successfully!\n";