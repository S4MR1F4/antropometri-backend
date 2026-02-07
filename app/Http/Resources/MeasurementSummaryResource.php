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
            'date' => $this->measurement_date->toDateString(),
            'weight' => $this->weight,
            'height' => $this->height,
            'status_summary' => $this->getStatusSummary(),
            'age_at_measurement' => $this->formatAgeAtMeasurement(),
        ];
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
