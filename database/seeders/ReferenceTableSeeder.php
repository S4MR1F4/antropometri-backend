<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceTableSeeder extends Seeder
{
    /**
     * Run the database seeds for reference tables.
     * Contains sample data for testing based on WHO/Kemenkes standards.
     */
    public function run(): void
    {
        // 1. BB/U (Berat Badan per Umur) - Balita Laki-laki 23 bulan
        DB::table('reference_balita_bbu')->updateOrInsert(
            ['gender' => 'L', 'age_months' => 23],
            [
                'neg3sd' => 8.9,
                'neg2sd' => 9.8,
                'neg1sd' => 10.8,
                'median' => 12.0,
                'pos1sd' => 13.3,
                'pos2sd' => 14.8,
                'pos3sd' => 16.5,
            ]
        );

        // 2. TB/U (Tinggi Badan per Umur) - Balita Laki-laki 23 bulan
        DB::table('reference_balita_tbu')->updateOrInsert(
            ['gender' => 'L', 'age_months' => 23],
            [
                'neg3sd' => 78.7,
                'neg2sd' => 81.3,
                'neg1sd' => 84.1,
                'median' => 86.9,
                'pos1sd' => 89.8,
                'pos2sd' => 92.6,
                'pos3sd' => 95.5,
            ]
        );

        // 3. BB/TB (Berat Badan per Tinggi Badan) - Balita Laki-laki 60cm
        // (Sample test data use height 60cm and weight 10kg)
        DB::table('reference_balita_bbtb')->updateOrInsert(
            ['gender' => 'L', 'height' => 60.0],
            [
                'neg3sd' => 4.7,
                'neg2sd' => 5.1,
                'neg1sd' => 5.5,
                'median' => 6.0,
                'pos1sd' => 6.6,
                'pos2sd' => 7.2,
                'pos3sd' => 7.9,
            ]
        );

        // Special test for your current input: Height 60.0 cm
        // We added it above. Let's add a few more common heights
        DB::table('reference_balita_bbtb')->updateOrInsert(['gender' => 'L', 'height' => 80.0], [
            'neg3sd' => 8.6,
            'neg2sd' => 9.4,
            'neg1sd' => 10.3,
            'median' => 11.2,
            'pos1sd' => 12.3,
            'pos2sd' => 13.4,
            'pos3sd' => 14.7
        ]);

        $this->command->info('Reference sample data seeded successfully for testing.');
    }
}
