<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceTableSeeder extends Seeder
{
    /**
     * Run the database seeds for reference tables.
     * Populates BB/U, TB/U, BB/TB, and IMT/U based on WHO/Kemenkes standards.
     */
    public function run(): void
    {
        $this->seedBbu();
        $this->seedTbu();
        $this->seedBbtb();
        $this->seedImtu();

        $this->command->info('Reference tables seeded successfully with WHO/Kemenkes standards.');
    }

    private function seedBbu(): void
    {
        $this->command->info('Seeding BB/U (Weight-for-Age)...');

        $boys = ReferenceDataHelper::getBbuBoys();
        $girls = ReferenceDataHelper::getBbuGirls();

        $data = [];
        foreach ($boys as $age => $sd) {
            $data[] = $this->formatRow('L', $age, $sd, 'age_months');
        }
        foreach ($girls as $age => $sd) {
            $data[] = $this->formatRow('P', $age, $sd, 'age_months');
        }

        DB::table('reference_balita_bbu')->truncate();
        DB::table('reference_balita_bbu')->insert($data);
    }

    private function seedTbu(): void
    {
        $this->command->info('Seeding TB/U (Height-for-Age)...');

        $boys = ReferenceDataHelper::getTbuBoys();
        $girls = ReferenceDataHelper::getTbuGirls();

        $data = [];
        foreach ($boys as $age => $sd) {
            $data[] = $this->formatRow('L', $age, $sd, 'age_months');
        }
        foreach ($girls as $age => $sd) {
            $data[] = $this->formatRow('P', $age, $sd, 'age_months');
        }

        DB::table('reference_balita_tbu')->truncate();
        DB::table('reference_balita_tbu')->insert($data);
    }

    private function seedBbtb(): void
    {
        $this->command->info('Seeding BB/TB (Weight-for-Height)...');

        $boys = ReferenceDataHelper::getBbtbBoys();
        $girls = ReferenceDataHelper::getBbtbGirls();

        $data = [];
        foreach ($boys as $height => $sd) {
            $data[] = $this->formatRow('L', $height, $sd, 'height');
        }
        foreach ($girls as $height => $sd) {
            $data[] = $this->formatRow('P', $height, $sd, 'height');
        }

        DB::table('reference_balita_bbtb')->truncate();
        DB::table('reference_balita_bbtb')->insert($data);
    }

    private function seedImtu(): void
    {
        $this->command->info('Seeding IMT/U (BMI-for-Age) for Adolescents...');

        $boys = ReferenceDataHelper::getImtuBoys();
        $girls = ReferenceDataHelper::getImtuGirls();

        $data = [];
        foreach ($boys as $age => $sd) {
            $data[] = $this->formatRow('L', $age, $sd, 'age_months');
        }
        foreach ($girls as $age => $sd) {
            $data[] = $this->formatRow('P', $age, $sd, 'age_months');
        }

        DB::table('reference_remaja_imtu')->truncate();
        DB::table('reference_remaja_imtu')->insert($data);
    }

    private function formatRow(string $gender, $key, array $sd, string $keyName): array
    {
        return [
            'gender' => $gender,
            $keyName => $key,
            'neg3sd' => $sd[0],
            'neg2sd' => $sd[1],
            'neg1sd' => $sd[2],
            'median' => $sd[3],
            'pos1sd' => $sd[4],
            'pos2sd' => $sd[5],
            'pos3sd' => $sd[6],
        ];
    }
}
