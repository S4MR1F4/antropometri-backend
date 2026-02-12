<?php

namespace App\Actions\Measurement;

/**
 * Calculate Dewasa (>18 years) measurement results.
 * Per 08_calculation_logic.md §5
 */
class CalculateDewasaAction
{
    /**
     * Execute dewasa calculations.
     */
    public function execute(
        string $gender,
        float $weight,
        float $height,
        ?float $waistCircumference = null,
        ?float $armCircumference = null,
        bool $isPregnant = false,
    ): array {
        // Calculate BMI
        $bmi = $this->calculateBMI($weight, $height);
        $bmiResult = $this->classifyBMI($bmi);

        $result = [
            'bmi' => round($bmi, 2),
            'status_bmi' => $bmiResult['status'],
        ];

        // Check central obesity if waist circumference provided
        // valid only if NOT pregnant
        if ($waistCircumference !== null && !$isPregnant) {
            $obesityResult = $this->checkCentralObesity($waistCircumference, $gender);
            $result['has_central_obesity'] = $obesityResult['has_obesity'];
            $result['status_central_obesity'] = $obesityResult['status'];
        } elseif ($isPregnant) {
            $result['status_central_obesity'] = 'Normal (Hamil)'; // Override for pregnant
        }

        // Check LILA for Pregnant Women
        if ($isPregnant) {
            $result['status_bmi'] = 'Ibu Hamil (' . $bmiResult['status'] . ')';

            if ($armCircumference !== null) {
                $lilaResult = $this->checkLila($armCircumference);
                $result['status_lila'] = $lilaResult['status'];
                $result['status_kek'] = $lilaResult['status']; // Sync'd key
                $result['lila_actual'] = $armCircumference;
            }
        }

        return $result;
    }

    /**
     * Calculate BMI.
     * Per 08_calculation_logic.md §5.1
     */
    private function calculateBMI(float $weight, float $height): float
    {
        $heightInMeters = $height / 100;
        return $weight / ($heightInMeters * $heightInMeters);
    }

    /**
     * Classify BMI status (Kemenkes RI classification).
     * Per 08_calculation_logic.md §5.1
     */
    private function classifyBMI(float $bmi): array
    {
        $status = match (true) {
            $bmi < 17.0 => 'Sangat Kurus',
            $bmi < 18.5 => 'Kurus',
            $bmi <= 25.0 => 'Normal',
            $bmi <= 27.0 => 'Gemuk',
            default => 'Obesitas',
        };

        $reason = match (true) {
            $bmi < 17.0 => 'IMT < 17.0',
            $bmi < 18.5 => 'IMT 17.0 - 18.4',
            $bmi <= 25.0 => 'IMT 18.5 - 25.0',
            $bmi <= 27.0 => 'IMT 25.1 - 27.0',
            default => 'IMT > 27.0',
        };

        return ['status' => $status, 'reason' => $reason];
    }

    /**
     * Check central obesity based on waist circumference.
     * Per 08_calculation_logic.md §5.2
     */
    private function checkCentralObesity(float $waistCircumference, string $gender): array
    {
        $threshold = $gender === 'L' ? 90 : 80;
        $hasObesity = $waistCircumference > $threshold;

        return [
            'has_obesity' => $hasObesity,
            'status' => $hasObesity ? 'Obesitas Sentral' : 'Normal',
            'threshold' => $threshold,
            'actual' => $waistCircumference,
            'difference' => $waistCircumference - $threshold,
        ];
    }

    /**
     * Check LILA for KEK (Kekurangan Energi Kronis) in pregnant women.
     * Threshold < 23.5 cm
     */
    private function checkLila(float $armCircumference): array
    {
        $threshold = 23.5;
        $isKEK = $armCircumference < $threshold;

        return [
            'is_kek' => $isKEK,
            'status' => $isKEK ? 'Risiko KEK' : 'Normal',
            'threshold' => $threshold,
        ];
    }
}
