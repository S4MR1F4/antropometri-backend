<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Grouped history resource for unique subjects in Riwayat view.
 * Per approved implementation plan.
 */
class HistoryGroupedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nik' => $this->nik,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'measurement_count' => $this->measurements_count ?? 0,

            // Latest measurement formatted as a summary
            'latest_measurement' => $this->whenLoaded('latestMeasurement', function () {
                return new MeasurementSummaryResource($this->latestMeasurement);
            }),
        ];
    }
}
