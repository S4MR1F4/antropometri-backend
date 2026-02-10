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
            'imt' => round($bmi, 2),
            'status_imt' => $bmiResult['status'],
            'reason_imt' => $bmiResult['reason'],
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

        return [
            'has_obesity' => $waistCircumference > $threshold,
            'threshold' => $threshold,
            'actual' => $waistCircumference,
            'difference' => $waistCircumference - $threshold,
        ];
    }
}
