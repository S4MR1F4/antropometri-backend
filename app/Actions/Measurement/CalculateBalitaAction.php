<?php

namespace App\Actions\Measurement;

use App\Models\ReferenceBalitaBbu;
use App\Models\ReferenceBalitaTbu;
use App\Models\ReferenceBalitaBbtb;

/**
 * Calculate Balita (0-60 months) measurement results.
 * Per 08_calculation_logic.md §3
 */
class CalculateBalitaAction
{
    /**
     * Execute all balita calculations.
     */
    public function execute(
        string $gender,
        int $ageInMonths,
        float $weight,
        float $height,
        ?string $measurementType = null,
    ): array {
        // Height adjustment per 08_calculation_logic.md §3.3
        $adjustedHeight = $this->adjustHeight($height, $ageInMonths, $measurementType);

        // Calculate all Z-scores
        $bbu = $this->calculateBBU($gender, $ageInMonths, $weight);
        $tbu = $this->calculateTBU($gender, $ageInMonths, $adjustedHeight);
        $bbtb = $this->calculateBBTB($gender, $adjustedHeight, $weight);

        return [
            'zscore_bbu' => $bbu['zscore'],
            'status_bbu' => $bbu['status'],
            'reason_bbu' => $bbu['reason'],
            'zscore_tbu' => $tbu['zscore'],
            'status_tbu' => $tbu['status'],
            'reason_tbu' => $tbu['reason'],
            'zscore_bbtb' => $bbtb['zscore'],
            'status_bbtb' => $bbtb['status'],
            'reason_bbtb' => $bbtb['reason'],
        ];
    }

    /**
     * Adjust height based on measurement type.
     * Per 08_calculation_logic.md §3.3
     */
    private function adjustHeight(float $height, int $ageInMonths, ?string $measurementType): float
    {
        if ($measurementType === null) {
            return $height;
        }

        // If age < 24 months AND measured standing, add 0.7 cm
        if ($ageInMonths < 24 && $measurementType === 'berdiri') {
            return $height + 0.7;
        }

        // If age >= 24 months AND measured lying, subtract 0.7 cm
        if ($ageInMonths >= 24 && $measurementType === 'berbaring') {
            return $height - 0.7;
        }

        return $height;
    }

    /**
     * Calculate BB/U (Weight-for-Age) Z-score.
     * Per 08_calculation_logic.md §3.2
     */
    private function calculateBBU(string $gender, int $ageInMonths, float $weight): array
    {
        // Cap age at 60 months for table lookup
        $lookupAge = min($ageInMonths, 60);

        $reference = ReferenceBalitaBbu::where('gender', $gender)
            ->where('age_months', $lookupAge)
            ->first();

        if (!$reference) {
            return ['zscore' => null, 'status' => 'Data Tidak Tersedia'];
        }

        $zscore = $this->calculateZScore($weight, $reference);

        $status = match (true) {
            $zscore < -3 => 'Gizi Buruk',
            $zscore < -2 => 'Gizi Kurang',
            $zscore <= 1 => 'Gizi Baik',
            default => 'Berisiko Gizi Lebih',
        };

        $reason = match (true) {
            $zscore < -3 => 'Z-Score < -3 SD',
            $zscore < -2 => 'Z-Score -3 s/d < -2 SD',
            $zscore <= 1 => 'Z-Score -2 s/d +1 SD',
            default => 'Z-Score > +1 SD',
        };

        return ['zscore' => round($zscore, 2), 'status' => $status, 'reason' => $reason];
    }

    /**
     * Calculate TB/U (Height-for-Age) Z-score.
     * Per 08_calculation_logic.md §3.3
     */
    private function calculateTBU(string $gender, int $ageInMonths, float $height): array
    {
        $lookupAge = min($ageInMonths, 60);

        $reference = ReferenceBalitaTbu::where('gender', $gender)
            ->where('age_months', $lookupAge)
            ->first();

        if (!$reference) {
            return ['zscore' => null, 'status' => 'Data Tidak Tersedia'];
        }

        $zscore = $this->calculateZScore($height, $reference);

        $status = match (true) {
            $zscore < -3 => 'Sangat Pendek',
            $zscore < -2 => 'Pendek',
            $zscore <= 3 => 'Normal',
            default => 'Tinggi',
        };

        $reason = match (true) {
            $zscore < -3 => 'Z-Score < -3 SD',
            $zscore < -2 => 'Z-Score -3 s/d < -2 SD',
            $zscore <= 3 => 'Z-Score -2 s/d +3 SD',
            default => 'Z-Score > +3 SD',
        };

        return ['zscore' => round($zscore, 2), 'status' => $status, 'reason' => $reason];
    }

    /**
     * Calculate BB/TB (Weight-for-Height) Z-score.
     * Per 08_calculation_logic.md §3.4
     */
    private function calculateBBTB(string $gender, float $height, float $weight): array
    {
        // Round height to nearest 0.5 for lookup
        $lookupHeight = round($height * 2) / 2;

        $reference = ReferenceBalitaBbtb::where('gender', $gender)
            ->where('height', $lookupHeight)
            ->first();

        // Try interpolation if exact match not found
        if (!$reference) {
            $reference = $this->findClosestHeightReference($gender, $height);
        }

        if (!$reference) {
            return ['zscore' => null, 'status' => 'Data Tidak Tersedia'];
        }

        $zscore = $this->calculateZScore($weight, $reference);

        $status = match (true) {
            $zscore < -3 => 'Gizi Buruk',
            $zscore < -2 => 'Gizi Kurang',
            $zscore <= 1 => 'Gizi Baik',
            $zscore <= 2 => 'Berisiko Gizi Lebih',
            $zscore <= 3 => 'Gizi Lebih',
            default => 'Obesitas',
        };

        $reason = match (true) {
            $zscore < -3 => 'Z-Score < -3 SD',
            $zscore < -2 => 'Z-Score -3 s/d < -2 SD',
            $zscore <= 1 => 'Z-Score -2 s/d +1 SD',
            $zscore <= 2 => 'Z-Score +1 s/d +2 SD',
            $zscore <= 3 => 'Z-Score +2 s/d +3 SD',
            default => 'Z-Score > +3 SD',
        };

        return ['zscore' => round($zscore, 2), 'status' => $status, 'reason' => $reason];
    }

    /**
     * Find closest height reference for interpolation.
     */
    private function findClosestHeightReference(string $gender, float $height)
    {
        return ReferenceBalitaBbtb::where('gender', $gender)
            ->orderByRaw('ABS(height - ?)', [$height])
            ->first();
    }

    /**
     * Calculate Z-score using reference data.
     * Per 08_calculation_logic.md §3.1
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
