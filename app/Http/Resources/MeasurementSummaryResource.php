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
                'nik' => $this->subject->getMaskedNik(),
                'date_of_birth' => $this->subject->date_of_birth->toDateString(),
            ],
            'petugas_name' => $this->user?->name,
            'measurement_date' => $this->measurement_date->toDateString(), // YYYY-MM-DD
            'measured_at' => $this->created_at?->toIso8601String(), // Full datetime
            'category' => $this->category,
            'weight' => $this->weight,
            'height' => $this->height,
            'head_circumference' => $this->when($this->category === 'balita', $this->head_circumference),
            'waist_circumference' => $this->when($this->category === 'dewasa', $this->waist_circumference),
            'arm_circumference' => $this->arm_circumference,
            'is_pregnant' => $this->is_pregnant,
            'age_in_months' => $this->age_in_months,

            // FULL RESULT for Mobile App Compatibility
            'result' => $this->getFlatResult(),

            'recommendation' => $this->recommendation,
            'notes' => $this->notes,
        ];
    }

    /**
     * Get flat result array compatible with Mobile App's Result model.
     * Copied from MeasurementResource for consistency in Lists/History.
     */
    private function getFlatResult(): array
    {
        // Base fields present in the DB
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

        // Pregnancy Logic Override
        if ($this->is_pregnant) {
            $flat['central_obesity_status'] = 'Normal (Hamil)';

            if ($this->arm_circumference !== null) {
                // Determine LILA status
                $flat['status_lila'] = $this->arm_circumference < 23.5 ? 'Risiko KEK' : 'Normal';
                $flat['lila'] = $this->arm_circumference;
            }
        }

        return $flat;
    }
}
