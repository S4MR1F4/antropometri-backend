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
    ): array {
        // Calculate BMI
        $bmi = $this->calculateBMI($weight, $height);
        $bmiResult = $this->classifyBMI($bmi);

        $result = [
            'imt' => round($bmi, 2),
            'status_imt' => $bmiResult['status'],
        ];

        // Check central obesity if waist circumference provided
        if ($waistCircumference !== null) {
            $obesityResult = $this->checkCentralObesity($waistCircumference, $gender);
            $result['has_central_obesity'] = $obesityResult['has_obesity'];
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
            $bmi < 17.0 => 'Kurus Tingkat Berat',
            $bmi < 18.5 => 'Kurus Tingkat Ringan',
            $bmi <= 25.0 => 'Normal',
            $bmi <= 27.0 => 'Gemuk Tingkat Ringan',
            default => 'Gemuk Tingkat Berat',
        };

        return ['status' => $status];
    }

    /**
     * Check central obesity based on waist circumference.
     * Per 08_calculation_logic.md §5.2
     */
    private function checkCentralObesity(float $waistCircumference, string $gender): array
    {
        $threshold = $gender === 'L' ? 90 : 80;

        return [
            'has_obesity' => $waistCircumference > $threshold,
            'threshold' => $threshold,
            'actual' => $waistCircumference,
            'difference' => $waistCircumference - $threshold,
        ];
    }
}
