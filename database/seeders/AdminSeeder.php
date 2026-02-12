<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * AdminSeeder - Creates default admin account
 * Per implementation_plan.md Phase 4
 */
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'antropometri@samrifa.com'],
            [
                'name' => 'Administrator Antropometri',
                'password' => Hash::make('tOOr12345*'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Default admin account created: antropometri@samrifa.com');
    }
}
