<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * PetugasSeeder - Creates default staff/petugas account
 */
class PetugasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'petugas@antropometri.go.id'],
            [
                'name' => 'Petugas Lapangan',
                'password' => Hash::make('Petugas@123'),
                'role' => 'petugas',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Default petugas account created: petugas@antropometri.go.id');
    }
}
