<?php

namespace App\Services;

use App\Actions\Measurement\CalculateBalitaAction;
use App\Actions\Measurement\CalculateDewasaAction;
use App\Actions\Measurement\CalculateRemajaAction;
use App\Models\Subject;

/**
 * Calculation service - orchestrates calculation logic.
 * Per 11_backend_architecture_laravel.md §5
 * Per 08_calculation_logic.md
 */
class CalculationService
{
    public function __construct(
        protected CalculateBalitaAction $balitaAction,
        protected CalculateRemajaAction $remajaAction,
        protected CalculateDewasaAction $dewasaAction,
    ) {
    }

    /**
     * Calculate measurement results based on category.
     */
    public function calculate(
        Subject $subject,
        array $measurementData,
        int $ageInMonths,
        string $category
    ): array {
        $results = match ($category) {
            'balita' => $this->balitaAction->execute(
                gender: $subject->gender,
                ageInMonths: $ageInMonths,
                weight: $measurementData['weight'],
                height: $measurementData['height'],
                measurementType: $measurementData['measurement_type'] ?? null,
            ),
            'remaja' => $this->remajaAction->execute(
                gender: $subject->gender,
                ageInMonths: $ageInMonths,
                weight: $measurementData['weight'],
                height: $measurementData['height'],
            ),
            'dewasa' => $this->dewasaAction->execute(
                gender: $subject->gender,
                weight: $measurementData['weight'],
                height: $measurementData['height'],
                waistCircumference: $measurementData['waist_circumference'] ?? null,
                armCircumference: $measurementData['arm_circumference'] ?? null,
                isPregnant: $measurementData['is_pregnant'] ?? false,
            ),
            default => [],
        };

        $results['references'] = $this->generateReferenceInformation($category, $subject->gender, $measurementData, $results);

        return $results;
    }

    private function generateReferenceInformation(string $category, string $gender, array $data, array $results): array
    {
        return match ($category) {
            'balita' => [
                'BB/U' => 'Standar: -2 SD s/d +1 SD',
                'TB/U' => 'Standar: -2 SD s/d +3 SD',
                'BB/TB' => 'Standar: -2 SD s/d +1 SD',
            ],
            'remaja' => [
                'IMT/U' => 'Standar: -2 SD s/d +1 SD',
            ],
            'dewasa' => array_filter([
                'IMT' => 'Ideal: 18.5 - 25.0',
                'Lingkar Perut' => isset($data['waist_circumference'])
                    ? ($gender === 'L' ? 'Normal: ≤ 90 cm' : 'Normal: ≤ 80 cm')
                    : null,
                'LILA' => isset($data['arm_circumference']) ? 'Normal: ≥ 23.5 cm' : null,
            ]),
            default => [],
        };
    }

    /**
     * Generate recommendation based on results.
     * Per 08_calculation_logic.md §7
     */
    public function generateRecommendation(string $category, array $results, array $data = []): string
    {
        $recommendation = match ($category) {
            'balita' => $this->generateBalitaRecommendation($results),
            'remaja' => $this->generateRemajaRecommendation($results),
            'dewasa' => $this->generateDewasaRecommendation($results),
            default => '',
        };

        // Add Pregnancy/LILA recommendation if applicable
        if (($data['is_pregnant'] ?? false) && isset($data['arm_circumference'])) {
            if ($data['arm_circumference'] < 23.5) {
                $recommendation .= ' Berisiko KEK (LILA < 23.5cm). Tingkatkan asupan gizi dan konsultasi dokter.';
            } else {
                $recommendation .= ' Lingkar Lengan Atas (LILA) Normal.';
            }
        }

        return $recommendation;
    }

    private function generateBalitaRecommendation(array $results): string
    {
        $recommendations = [];

        $bbtbStatus = $results['status_bbtb'] ?? null;
        $tbuStatus = $results['status_tbu'] ?? null;
        $bbuStatus = $results['status_bbu'] ?? null;

        if (in_array($bbtbStatus, ['Gizi Buruk', 'Gizi Kurang'])) {
            $recommendations[] = "Status Gizi ($bbtbStatus): Segera konsultasi ke RS/Puskesmas. Berikan MP-ASI kaya protein hewani (telur, hati, ikan) dan lemak sehat.";
        }

        if (in_array($tbuStatus, ['Sangat Pendek', 'Pendek'])) {
            $recommendations[] = "Indikasi Stunting ($tbuStatus): Pastikan asupan protein hewani setiap makan. Perhatikan sanitasi lingkungan dan akses air bersih.";
        }

        if (in_array($bbtbStatus, ['Obesitas', 'Gizi Lebih'])) {
            $recommendations[] = "Risiko Obesitas ($bbtbStatus): Batasi konsumsi gula dan camilan olahan. Tingkatkan aktivitas bermain aktif di luar ruangan.";
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Pertumbuhan anak dalam batas normal. Lanjutkan pemberian makanan bergizi seimbang dan imunisasi rutin.';
        }

        return implode(' ', $recommendations);
    }

    private function generateRemajaRecommendation(array $results): string
    {
        $status = $results['status_imtu'] ?? null;

        return match ($status) {
            'Gizi Buruk', 'Gizi Kurang' => 'Status Gizi Kurang: Tingkatkan asupan kalori dan protein. Hindari diet ketat tanpa pengawasan medis.',
            'Berisiko Gizi Lebih', 'Gizi Lebih', 'Obesitas' => 'Risiko Obesitas: Kurangi konsumsi minuman manis dan makanan cepat saji. Lakukan aktivitas fisik minimal 60 menit setiap hari.',
            default => 'Status gizi normal. Pertahankan pola makan bergizi dan gaya hidup aktif untuk masa pertumbuhan yang optimal.',
        };
    }

    private function generateDewasaRecommendation(array $results): string
    {
        $recommendations = [];
        $status = $results['status_bmi'] ?? null;

        if (in_array($status, ['Sangat Kurus', 'Kurus'])) {
            $recommendations[] = 'Tingkatkan asupan kalori dengan makanan bergizi.';
            $recommendations[] = 'Konsultasikan dengan ahli gizi jika diperlukan.';
        } elseif (in_array($status, ['Gemuk', 'Obesitas'])) {
            $recommendations[] = 'Kurangi asupan kalori berlebih.';
            $recommendations[] = 'Tingkatkan aktivitas fisik minimal 150 menit per minggu.';
        } else {
            $recommendations[] = 'Status gizi normal, pertahankan pola hidup sehat.';
        }

        // Check central obesity
        if (isset($results['has_central_obesity']) && $results['has_central_obesity']) {
            $recommendations[] = 'Perhatikan lingkar perut melebihi batas normal (L: ≤90cm, P: ≤80cm).';
            $recommendations[] = 'Konsultasi dokter untuk pemeriksaan risiko penyakit metabolik.';
        }

        return implode(' ', $recommendations);
    }
}
