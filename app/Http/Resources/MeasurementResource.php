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
            'user_id' => $this->user_id,
            'petugas_name' => $this->user?->name,
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

            // Singular 'result' key with FLAT structure for Mobile App compatibility
            'result' => $this->getFlatResult(),

            'references' => $this->reference_data,
            'recommendation' => $this->recommendation,
            'trend_info' => $this->trend_info,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Get flat result array compatible with Mobile App's Result model.
     */
    private function getFlatResult(): array
    {
        $flat = [
            'bmi' => $this->bmi,
            'bmi_status' => $this->status_bmi,
            'zscore_bbu' => $this->zscore_bbu,
            'status_bbu' => $this->status_bbu,
            'zscore_tbu' => $this->zscore_tbu,
            'status_tbu' => $this->status_tbu,
            'zscore_bbtb' => $this->zscore_bbtb,
            'status_bbtb' => $this->status_bbtb,
            'zscore_imtu' => $this->zscore_imtu,
            'status_imtu' => $this->status_imtu,

            // Aliases for Result.dart
            'status_imt' => $this->status_bmi,
            'imt' => $this->bmi,
        ];

        // Central Obesity
        if ($this->category === 'dewasa') {
            if ($this->has_central_obesity === true || $this->has_central_obesity === 1) {
                $flat['central_obesity_status'] = 'Obesitas Sentral';
            } elseif ($this->has_central_obesity === false || $this->has_central_obesity === 0) {
                $flat['central_obesity_status'] = 'Normal';
            } else {
                // Fallback to calculation if field is null
                if ($this->waist_circumference !== null && !$this->is_pregnant) {
                    $threshold = $this->subject?->gender === 'L' ? 90 : 80;
                    $flat['central_obesity_status'] = $this->waist_circumference > $threshold ? 'Obesitas Sentral' : 'Normal';
                }
            }

            if ($this->is_pregnant) {
                $flat['central_obesity_status'] = 'Normal (Hamil)';
            }
        }

        if ($this->arm_circumference !== null && $this->status_lila) {
            $flat['status_lila'] = $this->status_lila;
        }

        // Include recommendation in flat result for convenience
        $flat['recommendation'] = $this->recommendation;
        $flat['trend'] = $this->trend_info;
        $flat['references'] = $this->reference_data;

        return $flat;
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
