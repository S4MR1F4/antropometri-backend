<?php

namespace App\Actions\Measurement;

use App\Models\ReferenceRemajaImtu;

/**
 * Calculate Remaja (5-18 years) measurement results.
 * Per 08_calculation_logic.md §4
 */
class CalculateRemajaAction
{
    /**
     * Execute remaja IMT/U calculation.
     */
    public function execute(
        string $gender,
        int $ageInMonths,
        float $weight,
        float $height,
    ): array {
        // Calculate BMI
        $bmi = $this->calculateBMI($weight, $height);

        // Calculate Z-score
        $imtu = $this->calculateIMTU($gender, $ageInMonths, $bmi);

        return [
            'imt' => round($bmi, 2),
            'zscore_imtu' => $imtu['zscore'],
            'status_imtu' => $imtu['status'],
            'reason_imtu' => $imtu['reason'],
        ];
    }

    /**
     * Calculate BMI.
     * Per 08_calculation_logic.md §4.1
     */
    private function calculateBMI(float $weight, float $height): float
    {
        $heightInMeters = $height / 100;
        return $weight / ($heightInMeters * $heightInMeters);
    }

    /**
     * Calculate IMT/U (BMI-for-Age) Z-score.
     * Per 08_calculation_logic.md §4.1
     */
    private function calculateIMTU(string $gender, int $ageInMonths, float $bmi): array
    {
        // Cap age at valid range (61-216 months)
        $lookupAge = max(61, min($ageInMonths, 216));

        $reference = ReferenceRemajaImtu::where('gender', $gender)
            ->where('age_months', $lookupAge)
            ->first();

        if (!$reference) {
            return ['zscore' => null, 'status' => 'Data Tidak Tersedia', 'reason' => null];
        }

        $zscore = $this->calculateZScore($bmi, $reference);

        $status = match (true) {
            $zscore < -3 => 'Gizi Buruk',
            $zscore < -2 => 'Gizi Kurang',
            $zscore <= 1 => 'Gizi Baik',
            $zscore <= 2 => 'Gizi Lebih',
            default => 'Obesitas',
        };

        $reason = match (true) {
            $zscore < -3 => 'Z-Score < -3 SD',
            $zscore < -2 => 'Z-Score -3 s/d < -2 SD',
            $zscore <= 1 => 'Z-Score -2 s/d +1 SD',
            $zscore <= 2 => 'Z-Score +1 s/d +2 SD',
            default => 'Z-Score > +2 SD',
        };

        return ['zscore' => round($zscore, 2), 'status' => $status, 'reason' => $reason];
    }

    /**
     * Calculate Z-score using reference data.
     */
    private function calculateZScore(float $value, $reference): float
    {
        $median = $reference->median;
        $pos1sd = $reference->pos1sd;
        $neg1sd = $reference->neg1sd;

        if ($value >= $median) {
            $sd = $pos1sd - $median;
        } else {
            $sd = $median - $neg1sd;
        }

        if ($sd == 0) {
            return 0;
        }

        return ($value - $median) / $sd;
    }
}
