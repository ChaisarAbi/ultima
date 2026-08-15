<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Manajer Utama',
            'email' => 'manajer@bengkel.test',
            'password' => Hash::make('password'),
            'role' => 'manajer',
        ]);

        User::create([
            'name' => 'Staff Office',
            'email' => 'office@bengkel.test',
            'password' => Hash::make('password'),
            'role' => 'office',
        ]);

        User::create([
            'name' => 'Teknisi Andi',
            'email' => 'teknisi1@bengkel.test',
            'password' => Hash::make('password'),
            'role' => 'teknisi',
        ]);

        User::create([
            'name' => 'Teknisi Budi',
            'email' => 'teknisi2@bengkel.test',
            'password' => Hash::make('password'),
            'role' => 'teknisi',
        ]);

        $this->command->info('✅ 4 user berhasil dibuat:');
        $this->command->info('   - manajer@bengkel.test / password (role: manajer)');
        $this->command->info('   - office@bengkel.test / password (role: office)');
        $this->command->info('   - teknisi1@bengkel.test / password (role: teknisi)');
        $this->command->info('   - teknisi2@bengkel.test / password (role: teknisi)');
    }
}