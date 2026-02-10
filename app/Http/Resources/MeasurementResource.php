<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full measurement resource for API responses.
 * Per 07_api_specification.md §4.1
 */
class MeasurementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'measurement_date' => $this->measurement_date->toDateString(),
            'category' => $this->category,
            'weight' => $this->weight,
            'height' => $this->height,
            'head_circumference' => $this->when($this->category === 'balita', $this->head_circumference),
            'waist_circumference' => $this->when($this->category === 'dewasa', $this->waist_circumference),
            'arm_circumference' => $this->arm_circumference,
            'is_pregnant' => $this->is_pregnant,
            'measurement_type' => $this->when($this->category === 'balita', $this->measurement_type),
            'age_in_months' => $this->age_in_months,
            'age_in_years' => $this->when($this->category === 'dewasa', fn() => intdiv($this->age_in_months ?? 0, 12)),
            'results' => $this->getResults(),
            'references' => $this->reference_data,
            'recommendation' => $this->recommendation,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Get calculation results based on category.
     */
    private function getResults(): array
    {
        return match ($this->category) {
            'balita' => $this->getBalitaResults(),
            'remaja' => $this->getRemajaResults(),
            'dewasa' => $this->getDewasaResults(),
            default => [],
        };
    }

    private function getBalitaResults(): array
    {
        return [
            'bbu' => [
                'zscore' => $this->zscore_bbu,
                'status' => $this->status_bbu,
                'status_code' => $this->toStatusCode($this->status_bbu),
                'color' => $this->getStatusColor($this->status_bbu),
            ],
            'tbu' => [
                'zscore' => $this->zscore_tbu,
                'status' => $this->status_tbu,
                'status_code' => $this->toStatusCode($this->status_tbu),
                'color' => $this->getStatusColor($this->status_tbu),
            ],
            'bbtb' => [
                'zscore' => $this->zscore_bbtb,
                'status' => $this->status_bbtb,
                'status_code' => $this->toStatusCode($this->status_bbtb),
                'color' => $this->getStatusColor($this->status_bbtb),
            ],
        ];
    }

    private function getRemajaResults(): array
    {
        return [
            'imtu' => [
                'bmi' => $this->imt,
                'zscore' => $this->zscore_imtu,
                'status' => $this->status_imtu,
                'status_code' => $this->toStatusCode($this->status_imtu),
                'color' => $this->getStatusColor($this->status_imtu),
            ],
        ];
    }

    private function getDewasaResults(): array
    {
        $results = [
            'bmi' => [
                'value' => $this->imt,
                'status' => $this->status_imt,
                'status_code' => $this->toStatusCode($this->status_imt),
                'color' => $this->getStatusColor($this->status_imt),
            ],
        ];

        if ($this->waist_circumference !== null) {
            $threshold = $this->resource->subject?->gender === 'L' ? 90 : 80;
            $results['central_obesity'] = [
                'has_obesity' => $this->waist_circumference > $threshold,
                'threshold' => $threshold,
                'actual' => $this->waist_circumference,
            ];
        }

        return $results;
    }

    private function toStatusCode(?string $status): string
    {
        if ($status === null) {
            return 'unknown';
        }

        return strtolower(str_replace(' ', '_', $status));
    }

    private function getStatusColor(?string $status): string
    {
        return match ($status) {
            'Gizi Baik', 'Normal' => '#388E3C',
            'Gizi Kurang', 'Pendek', 'Kurus Tingkat Ringan', 'Gizi Lebih' => '#F57C00',
            'Gizi Buruk', 'Sangat Pendek', 'Kurus Tingkat Berat', 'Gemuk Tingkat Berat', 'Obesitas' => '#D32F2F',
            'Berisiko Gizi Lebih', 'Tinggi', 'Gemuk Tingkat Ringan' => '#FBC02D',
            default => '#757575',
        };
    }
}
