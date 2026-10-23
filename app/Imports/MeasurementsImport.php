<?php

namespace App\Imports;

use App\Models\Measurement;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use App\Services\MeasurementService;

class MeasurementsImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    public $importedCount = 0;
    public $skippedCount = 0;

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row = $row->toArray();
            // Mapping based on template headers (slugified by WithHeadingRow)
            // Example: "Nama Pasien" -> "nama_pasien", "Tanggal Lahir" -> "tanggal_lahir"
            
            $name = $row['nama_pasien'] ?? null;

            // Skip empty rows or template filler rows
            if (empty($name) || trim($name) === '' || trim($name) === 'Lengkapi Data!') {
                $this->skippedCount++;
                continue;
            }

            try {
                // Parse dates
                $dob = $this->parseDate($row['tanggal_lahir'] ?? null);
                $measurementDate = $this->parseDate($row['tanggal_periksa'] ?? null);

                if (!$dob || !$measurementDate || !$name) {
                    $this->skippedCount++;
                    continue;
                }

                $gender = isset($row['jenis_kelamin_lp']) ? strtoupper(trim($row['jenis_kelamin_lp'])) : 'L';
                if (!in_array($gender, ['L', 'P'])) {
                    $gender = 'L'; // Default fallback
                }

                $isPregnant = isset($row['hamil_yt']) && strtoupper(trim($row['hamil_yt'])) === 'Y';

                $userId = auth()->id() ?? 1;
                $normalizedName = Subject::normalizeName($name);

                // Find or create subject with proper tenant and normalized name
                $subject = Subject::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'normalized_name' => $normalizedName,
                        'date_of_birth' => $dob,
                    ],
                    [
                        'name' => $name,
                        'gender' => $gender,
                    ]
                );

                $weight = $this->parseNumeric($row['berat_badan_kg'] ?? null);
                $height = $this->parseNumeric($row['tinggi_badan_cm'] ?? null);
                $lila = $this->parseNumeric($row['lila_cm'] ?? null);
                $headCirc = $this->parseNumeric($row['lingkar_kepala_cm'] ?? null);

                // Check if identical measurement already exists to avoid duplicates
                $existing = Measurement::where('subject_id', $subject->id)
                    ->whereDate('measurement_date', $measurementDate)
                    ->first();

                if ($existing) {
                    $this->skippedCount++;
                    continue; // Skip duplicate
                }

                // Create measurement via service to trigger all calculations & Z-scores
                app(MeasurementService::class)->createMeasurement($subject, [
                    'user_id' => $userId,
                    'measurement_date' => $measurementDate,
                    'weight' => $weight,
                    'height' => $height,
                    'arm_circumference' => $lila,
                    'head_circumference' => $headCirc,
                    'is_pregnant' => $isPregnant,
                ]);

                $this->importedCount++;

            } catch (\Exception $e) {
                Log::error('Import error row: ' . json_encode($row) . ' Error: ' . $e->getMessage());
                $this->skippedCount++;
            }
        }
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseNumeric($value)
    {
        if (empty($value)) return null;
        if (is_numeric($value)) return (float) $value;
        $val = str_replace(',', '.', $value);
        return is_numeric($val) ? (float) $val : null;
    }
}
