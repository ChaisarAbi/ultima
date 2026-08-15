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
use Illuminate\Support\Facades\DB;

class BigDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding 200+ records...');

        // ==================== USERS (4 existing) ====================
        $manager = User::firstOrCreate(
            ['email' => 'manajer@bengkel.test'],
            ['name' => 'Manajer Bengkel', 'password' => 'password', 'role' => 'manajer']
        );
        $office  = User::firstOrCreate(
            ['email' => 'office@bengkel.test'],
            ['name' => 'Office Admin', 'password' => 'password', 'role' => 'office']
        );
        $teknisi1 = User::firstOrCreate(
            ['email' => 'budi@bengkel.test'],
            ['name' => 'Budi Teknisi', 'password' => 'password', 'role' => 'teknisi']
        );
        $teknisi2 = User::firstOrCreate(
            ['email' => 'ani@bengkel.test'],
            ['name' => 'Ani Teknisi', 'password' => 'password', 'role' => 'teknisi']
        );
        $teknisi3 = User::firstOrCreate(
            ['email' => 'dodi@bengkel.test'],
            ['name' => 'Dodi Mekanik', 'password' => 'password', 'role' => 'teknisi']
        );
        $teknisi4 = User::firstOrCreate(
            ['email' => 'eva@bengkel.test'],
            ['name' => 'Eva Mekanik', 'password' => 'password', 'role' => 'teknisi']
        );

        $teknisis = [$teknisi1, $teknisi2, $teknisi3, $teknisi4];

        // ==================== CUSTOMERS (25) ====================
        $customerNames = [
            'Ahmad Syarif', 'Siti Nurhaliza', 'Rudi Hartono', 'Dewi Sartika', 'Bambang Suprapto',
            'Ratna Kusuma', 'Hendra Gunawan', 'Maya Anggraini', 'Adi Pratama', 'Nina Wulandari',
            'Fajar Setiawan', 'Rina Marlina', 'Doni Lesmana', 'Sari Dewi', 'Agus Wijaya',
            'Tuti Alawiyah', 'Bayu Permana', 'Lina Nurhayati', 'Eko Prasetyo', 'Risa Amalia',
            'Hadi Suherman', 'Vina Septiani', 'Joko Susilo', 'Fitri Handayani', 'Reza Pahlevi',
        ];
        $customers = [];
        $phoneBase = 811000000;
        foreach ($customerNames as $i => $name) {
            $customers[] = Customer::create([
                'name'    => $name,
                'phone'   => '08' . strval($phoneBase + $i * 1111),
                'address' => 'Jl. Bengkel No. ' . ($i + 1) . ', Kota',
                'email'   => strtolower(str_replace(' ', '.', $name)) . '@email.com',
            ]);
        }

        // ==================== VEHICLES (40) ====================
        $brandsModels = [
            ['Toyota', 'Avanza', 'Silver'], ['Toyota', 'Innova', 'Hitam'], ['Toyota', 'Rush', 'Putih'],
            ['Toyota', 'Fortuner', 'Abu-abu'], ['Toyota', 'Calya', 'Merah'],
            ['Honda', 'Civic', 'Hitam'], ['Honda', 'CR-V', 'Putih'], ['Honda', 'HR-V', 'Silver'],
            ['Honda', 'Jazz', 'Kuning'], ['Honda', 'Brio', 'Merah'],
            ['Suzuki', 'Ertiga', 'Putih'], ['Suzuki', 'XL7', 'Hitam'], ['Suzuki', 'Karimun', 'Silver'],
            ['Suzuki', 'Ignis', 'Orange'], ['Suzuki', 'Baleno', 'Abu-abu'],
            ['Daihatsu', 'Sigra', 'Merah'], ['Daihatsu', 'Terios', 'Putih'], ['Daihatsu', 'Ayla', 'Biru'],
            ['Daihatsu', 'Xenia', 'Silver'], ['Daihatsu', 'Gran Max', 'Hitam'],
            ['Mitsubishi', 'Xpander', 'Hitam'], ['Mitsubishi', 'Pajero', 'Putih'], ['Mitsubishi', 'L300', 'Silver'],
            ['Nissan', 'Livina', 'Biru'], ['Nissan', 'March', 'Merah'],
            ['Hyundai', 'Santa Fe', 'Putih'], ['Hyundai', 'Palisade', 'Hitam'],
            ['Wuling', 'Confero', 'Silver'], ['Wuling', 'Almaz', 'Putih'], ['Wuling', 'Cortez', 'Abu-abu'],
            ['Mazda', 'CX-5', 'Merah'], ['Mazda', 'CX-3', 'Biru'],
            ['KIA', 'Seltos', 'Orange'], ['KIA', 'Sonet', 'Putih'],
            ['BMW', 'X5', 'Hitam'], ['Mercedes', 'GLC', 'Putih'],
            ['Audi', 'Q5', 'Silver'], ['Lexus', 'RX350', 'Abu-abu'],
            ['Volkswagen', 'Tiguan', 'Biru'], ['Chery', 'Omoda 5', 'Putih'],
        ];
        $vehicles = [];
        $plateChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        foreach ($brandsModels as $i => $bm) {
            $prefix = $plateChars[$i % 26];
            $number = 1000 + $i;
            $suffix = $plateChars[($i + 1) % 26] . $plateChars[($i + 2) % 26] . $plateChars[($i + 3) % 26];
            $plate = "{$prefix} {$number} {$suffix}";
            $vehicles[] = Vehicle::create([
                'plate_number' => $plate,
                'brand'        => $bm[0],
                'model'        => $bm[1],
                'year'         => 2018 + ($i % 6),
                'color'        => $bm[2],
                'customer_id'  => $customers[$i % count($customers)]->id,
            ]);
        }

        // ==================== SPARE PARTS (25) ====================
        $partsData = [
            ['Oli Mesin 10W-40', 'SP-001', 50, 5, 85000],
            ['Oli Mesin 5W-30', 'SP-002', 40, 5, 95000],
            ['Oli Mesin 20W-50', 'SP-003', 30, 5, 75000],
            ['Filter Oli', 'SP-004', 60, 10, 35000],
            ['Filter Udara', 'SP-005', 45, 10, 55000],
            ['Filter AC', 'SP-006', 35, 10, 65000],
            ['Busi Standar', 'SP-007', 80, 15, 25000],
            ['Busi Iridium', 'SP-008', 40, 10, 75000],
            ['Kampas Rem Depan', 'SP-009', 25, 5, 120000],
            ['Kampas Rem Belakang', 'SP-010', 20, 5, 100000],
            ['Piringan Rem', 'SP-011', 15, 3, 250000],
            ['Aki 12V 40Ah', 'SP-012', 12, 2, 450000],
            ['Aki 12V 60Ah', 'SP-013', 8, 2, 650000],
            ['Lampu Depan', 'SP-014', 30, 5, 85000],
            ['Lampu Belakang', 'SP-015', 35, 5, 55000],
            ['Ban Bridgestone 185/65', 'SP-016', 20, 4, 650000],
            ['Ban Michelin 195/60', 'SP-017', 15, 4, 850000],
            ['V-belt', 'SP-018', 40, 10, 45000],
            ['Timing Belt', 'SP-019', 25, 5, 150000],
            ['Radiator Coolant', 'SP-020', 30, 5, 65000],
            ['Minyak Rem', 'SP-021', 35, 5, 35000],
            ['Gardan Oil', 'SP-022', 25, 5, 55000],
            ['Wiper Blade', 'SP-023', 50, 10, 25000],
            ['Fuse 10A', 'SP-024', 100, 20, 5000],
            ['Fuse 15A', 'SP-025', 100, 20, 5000],
        ];
        $spareParts = [];
        foreach ($partsData as $pd) {
            $spareParts[] = SparePart::create([
                'name'          => $pd[0],
                'part_number'   => $pd[1],
                'stock'         => $pd[2],
                'minimum_stock' => $pd[3],
                'price'         => $pd[4],
            ]);
        }

        // ==================== SERVICES (220) ====================
        $serviceTypes = ['engine', 'body_repair', 'electrical', 'ac', 'tire', 'general'];
        $statuses = ['pending', 'progress', 'done', 'cancelled'];
        $services = [];

        $this->command->info('Creating 220 service orders...');
        $now = now();
        $bar = $this->command->getOutput()->createProgressBar(220);
        $bar->start();

        for ($i = 0; $i < 220; $i++) {
            $customer = $customers[$i % count($customers)];
            $vehicle = $vehicles[$i % count($vehicles)];
            $type = $serviceTypes[$i % count($serviceTypes)];
            $daysAgo = rand(0, 90);
            $entryDate = (clone $now)->subDays($daysAgo);

            // Determine status based on entry_date
            if ($daysAgo <= 3) {
                // Recent: mostly pending/progress
                $status = $i % 3 === 0 ? 'pending' : ($i % 3 === 1 ? 'progress' : 'done');
            } elseif ($daysAgo <= 7) {
                $status = $i % 4 === 0 ? 'pending' : ($i % 4 === 1 ? 'progress' : ($i % 4 === 2 ? 'done' : 'cancelled'));
            } else {
                $status = $i % 5 === 0 ? 'cancelled' : 'done';
            }

            $completionDate = null;
            $totalCost = 0;
            if ($status === 'done') {
                $hours = rand(1, 48);
                $completionDate = (clone $entryDate)->addHours($hours);
                $totalCost = rand(15, 200) * 10000;
            } elseif ($status === 'cancelled') {
                $completionDate = (clone $entryDate)->addDays(rand(1, 3));
            }

            $service = Service::create([
                'customer_name'  => $customer->name,
                'vehicle_plate'  => $vehicle->plate_number,
                'vehicle_id'     => $vehicle->id,
                'type'           => $type,
                'entry_date'     => $entryDate,
                'completion_date'=> $completionDate,
                'status'         => $status,
                'total_cost'     => $totalCost,
            ]);

            // Assign 1-2 technicians
            $techCount = rand(1, 2);
            $assigned = [];
            for ($t = 0; $t < $techCount; $t++) {
                $tech = $teknisis[array_rand($teknisis)];
                $assigned[] = $tech->id;
            }
            $service->technicians()->sync(array_unique($assigned));

            // If done, attach some spare parts
            if ($status === 'done' && rand(0, 1) === 1) {
                $numParts = rand(1, 4);
                for ($p = 0; $p < $numParts; $p++) {
                    $sp = $spareParts[array_rand($spareParts)];
                    $qty = rand(1, 3);
                    DB::table('service_spare_part')->insert([
                        'service_id'    => $service->id,
                        'spare_part_id' => $sp->id,
                        'quantity'      => $qty,
                        'price'         => $sp->price,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            $services[] = $service;
            $bar->advance();
        }
        $bar->finish();
        $this->command->info('');

        // ==================== OPERATIONAL LOGS (90 hari) ====================
        $this->command->info('Creating 90 operational logs for LSTM...');
        // Base pattern: weekday higher, weekend lower
        for ($i = 90; $i >= 0; $i--) {
            $date = (clone $now)->subDays($i);
            $dayOfWeek = (int)$date->format('N'); // 1=Mon, 7=Sun
            $isWeekend = $dayOfWeek >= 6;
            $base = $isWeekend ? rand(2, 5) : rand(5, 15);
            $totalSvc  = $base + rand(-1, 2);
            if ($totalSvc < 0) $totalSvc = 0;
            $completed = rand(1, min($totalSvc, 10));
            $totalRevenue = $completed * rand(5, 20) * 100000;
            OperationalLog::create([
                'log_date'             => $date->format('Y-m-d'),
                'total_services'       => $totalSvc,
                'completed_services'   => $completed,
                'avg_completion_hours' => round(rand(15, 80) / 10, 1),
                'total_spare_used'     => rand(1, 20),
                'total_revenue'        => $totalRevenue,
            ]);
        }

        // ==================== PREDICTION RESULTS (90 historis + 7 future) ====================
        $this->command->info('Creating prediction results...');
        for ($i = 90; $i >= 0; $i--) {
            $date = (clone $now)->subDays($i);
            // Simulate prediction that is close to actual
            $log = OperationalLog::where('log_date', $date->format('Y-m-d'))->first();
            $actual = $log ? $log->total_services : rand(3, 10);
            $predicted = $actual + rand(-2, 2);
            if ($predicted < 0) $predicted = 0;
            PredictionResult::create([
                'metric'          => 'total_services',
                'target_date'     => $date->format('Y-m-d'),
                'predicted_value' => $predicted,
                'actual_value'    => $actual,
                'model_version'   => 'lstm_v2',
                'generated_at'    => (clone $date)->addDay(),
            ]);
        }
        // Future predictions
        for ($i = 1; $i <= 14; $i++) {
            $date = (clone $now)->addDays($i);
            $dayOfWeek = (int)$date->format('N');
            $isWeekend = $dayOfWeek >= 6;
            $predicted = $isWeekend ? rand(2, 5) : rand(5, 12);
            PredictionResult::create([
                'metric'          => 'total_services',
                'target_date'     => $date->format('Y-m-d'),
                'predicted_value' => $predicted,
                'actual_value'    => null,
                'model_version'   => 'lstm_v2',
                'generated_at'    => now(),
            ]);
        }

        // ==================== ACTIVITY LOGS (230+) ====================
        $this->command->info('Creating activity logs...');
        $actionTemplates = [
            ['membuat servis', 'membuat servis baru untuk %s (%s)'],
            ['memperbarui servis', 'memperbarui status servis %s menjadi %s'],
            ['menambahkan spare part', 'menambahkan spare part ke servis %s'],
            ['menyelesaikan servis', 'menyelesaikan servis %s dengan biaya Rp %s'],
            ['membatalkan servis', 'membatalkan servis %s'],
            ['login', 'login ke sistem'],
            ['melihat laporan', 'melihat laporan bulanan'],
            ['generate prediksi', 'men-generate prediksi servis'],
        ];
        $users = [$manager, $office, ...$teknisis];

        $bar2 = $this->command->getOutput()->createProgressBar(230);
        $bar2->start();
        for ($i = 0; $i < 230; $i++) {
            $user = $users[array_rand($users)];
            $actionData = $actionTemplates[array_rand($actionTemplates)];
            $action = $actionData[0];

            $description = $actionData[1];
            if (strpos($description, '%s') !== false) {
                $svc = $services[array_rand($services)];
                if ($action === 'membuat servis') {
                    $description = sprintf($description, $svc->customer_name, $svc->vehicle_plate);
                } elseif ($action === 'memperbarui servis') {
                    $s = $statuses[array_rand($statuses)];
                    $description = sprintf($description, $svc->customer_name, $s);
                } elseif ($action === 'menyelesaikan servis') {
                    $description = sprintf($description, $svc->customer_name . ' - ' . $svc->vehicle_plate, number_format($svc->total_cost));
                } else {
                    $description = sprintf($description, $svc->customer_name . ' - ' . $svc->vehicle_plate);
                }
            }

            $daysAgo = rand(0, 90);
            $createdAt = (clone $now)->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => $action,
                'model_type'  => Service::class,
                'model_id'    => $services[array_rand($services)]->id,
                'description' => $description,
                'created_at'  => $createdAt,
                'updated_at'  => $createdAt,
            ]);
            $bar2->advance();
        }
        $bar2->finish();
        $this->command->info('');

        // ==================== REPORT ARCHIVES (10) ====================
        for ($i = 1; $i <= 10; $i++) {
            $month = $i;
            $isMonthly = $i % 3 !== 0;
            $reportDate = $isMonthly
                ? \Carbon\Carbon::create(2026, $month, 1)
                : \Carbon\Carbon::create(2026, 1, 1);
            $reportData = $isMonthly
                ? json_encode(['period' => 'monthly', 'month' => $month, 'year' => 2026])
                : json_encode(['period' => 'yearly', 'year' => 2026]);
            \App\Models\ReportArchive::create([
                'title'       => $isMonthly
                    ? 'Laporan Bulanan - ' . $reportDate->format('F') . ' 2026'
                    : 'Laporan Tahunan 2026',
                'type'        => $isMonthly ? 'bulanan' : 'tahunan',
                'report_date' => $reportDate->format('Y-m-d'),
                'file_path'   => 'reports/archive-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '.pdf',
                'report_data' => $reportData,
                'created_at'  => (clone $now)->subMonths($i),
                'updated_at'  => (clone $now)->subMonths($i),
            ]);
        }

        // ==================== SUMMARY ====================
        $this->command->info('');
        $this->command->info('=== SEEDING COMPLETE ===');
        $this->command->info('Customers: ' . Customer::count());
        $this->command->info('Vehicles: ' . Vehicle::count());
        $this->command->info('Services: ' . Service::count());
        $this->command->info('Spare Parts: ' . SparePart::count());
        $this->command->info('Operational Logs: ' . OperationalLog::count());
        $this->command->info('Prediction Results: ' . PredictionResult::count());
        $this->command->info('Activity Logs: ' . ActivityLog::count());
        $this->command->info('Report Archives: ' . \App\Models\ReportArchive::count());
    }
}