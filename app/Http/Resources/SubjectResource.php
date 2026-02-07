<?php

namespace App\Http\Resources;

use App\Services\SubjectService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Subject resource for API responses.
 * Per 07_api_specification.md §3
 */
class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subjectService = app(SubjectService::class);
        $ageInMonths = $subjectService->calculateAgeInMonths($this->date_of_birth);
        $category = $subjectService->determineCategory($ageInMonths);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'normalized_name' => $this->normalized_name,
            'date_of_birth' => $this->date_of_birth->toDateString(),
            'gender' => $this->gender,
            'nik' => $this->nik,
            'age_in_months' => $ageInMonths,
            'age_display' => $this->formatAge($ageInMonths),
            'category' => $category,
            'address' => $this->address,
            'parent_name' => $this->parent_name,
            'phone' => $this->phone,
            'measurements_count' => $this->whenCounted('measurements'),
            'latest_measurement' => $this->when(
                $this->relationLoaded('latestMeasurement') && $this->latestMeasurement,
                fn() => new MeasurementSummaryResource($this->latestMeasurement)
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Format age for display.
     */
    private function formatAge(int $months): string
    {
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
