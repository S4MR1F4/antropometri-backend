<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Measurement summary resource for list views.
 * Per 07_api_specification.md §4.2
 */
class MeasurementSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'subject' => [
                'id' => $this->subject->id,
                'name' => $this->subject->name,
                'gender' => $this->subject->gender,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'measurement_date' => $this->measurement_date->toDateString(),
            'category' => $this->category,
            'weight' => $this->weight,
            'height' => $this->height,
            'age_in_months' => $this->age_in_months,
            'status_summary' => $this->getStatusSummary(),
            'age_at_measurement' => $this->formatAgeAtMeasurement(),
            'results' => $this->getSummaryResults(),
            'recommendation' => $this->recommendation,
            'notes' => $this->notes,
        ];
    }

    private function getSummaryResults(): array
    {
        // Provide a minimal results structure for the model to parse
        return match ($this->category) {
            'balita' => [
                'bbu' => ['status' => $this->status_bbu],
                'tbu' => ['status' => $this->status_tbu],
                'bbtb' => ['status' => $this->status_bbtb],
            ],
            'remaja' => [
                'imtu' => ['status' => $this->status_imtu],
            ],
            'dewasa' => [
                'bmi' => ['status' => $this->status_imt],
            ],
            default => [],
        };
    }

    private function getStatusSummary(): string
    {
        // Priority: BB/TB > BB/U > TB/U for balita
        // For remaja: IMT/U
        // For dewasa: IMT
        return match ($this->category) {
            'balita' => $this->status_bbtb ?? $this->status_bbu ?? 'N/A',
            'remaja' => $this->status_imtu ?? 'N/A',
            'dewasa' => $this->status_imt ?? 'N/A',
            default => 'N/A',
        };
    }

    private function formatAgeAtMeasurement(): string
    {
        $months = $this->age_in_months;

        if ($months === null) {
            return 'N/A';
        }

        if ($months < 12) {
            return "{$months} bulan";
        }

        $years = intdiv($months, 12);
        $remainingMonths = $months % 12;

        if ($remainingMonths === 0) {
            return "{$years} tahun";
        }

        return "{$years} tahun {$remainingMonths} bulan";
    }
}
