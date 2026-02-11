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

            // Singular 'result' key with FLAT structure for Mobile App compatibility
            'result' => $this->getFlatResult(),

            'references' => $this->reference_data,
            'recommendation' => $this->recommendation,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Get flat result array compatible with Mobile App's Result model.
     */
    private function getFlatResult(): array
    {
        // Base fields present in the DB
        $flat = [
            'bmi' => $this->bmi,
            'bmi_status' => $this->status_bmi, // Ensure DB column matches or alias it
            'zscore_bbu' => $this->zscore_bbu,
            'status_bbu' => $this->status_bbu,
            'zscore_tbu' => $this->zscore_tbu,
            'status_tbu' => $this->status_tbu,
            'zscore_bbtb' => $this->zscore_bbtb,
            'status_bbtb' => $this->status_bbtb,
            'zscore_imtu' => $this->zscore_imtu,
            'status_imtu' => $this->status_imtu,

            // ALIASES for Mobile App (Result.dart compatibility)
            'status_bb_u' => $this->status_bbu,
            'bb_u_zscore' => $this->zscore_bbu,

            'status_tb_u' => $this->status_tbu,
            'tb_u_zscore' => $this->zscore_tbu,

            'status_bb_tb' => $this->status_bbtb,
            'bb_tb_zscore' => $this->zscore_bbtb,

            'status_imt_u' => $this->status_imtu,
            'imt_u_zscore' => $this->zscore_imtu,
        ];

        // Derived Logic for Central Obesity
        if ($this->category === 'dewasa' && $this->waist_circumference !== null) {
            $threshold = $this->subject?->gender === 'L' ? 90 : 80;
            $flat['central_obesity_status'] = $this->waist_circumference > $threshold
                ? 'Obesitas Sentral'
                : 'Normal';
        }

        // Pregnancy Logic Override (ensure it matches Action logic)
        if ($this->is_pregnant) {
            $flat['central_obesity_status'] = 'Normal (Hamil)';

            if ($this->arm_circumference !== null) {
                // Re-calculate or fetch from DB if stored. 
                // Assuming DB stores status_lila or we calculate on fly if not persisted.
                // For now, simple calc on fly for display if DB column missing
                $flat['status_lila'] = $this->arm_circumference < 23.5 ? 'Risiko KEK' : 'Normal';
                $flat['lila'] = $this->arm_circumference;
            }
        }

        // Aliases if necessary (e.g. mobile looks for 'weight_for_age_zscore')
        // Mobile Result.dart looks for 'zscore_bbu' or 'weight_for_age_zscore'.
        // The DB columns are 'zscore_bbu' etc., so we are handling it by returning them directly.

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
