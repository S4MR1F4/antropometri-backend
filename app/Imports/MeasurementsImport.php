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

class MeasurementsImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    public $importedCount = 0;
    public $skippedCount = 0;

    public function sheets(): array
    {
        return [
            // Handle specifically the 'Template Input' sheet if it exists, or the first sheet by index
            'Template Input' => $this,
            0 => $this // Fallback to first sheet
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row = $row->toArray();
            // Mapping based on template headers (slugified by WithHeadingRow)
            // Example: "Nama Pasien" -> "nama_pasien", "Tanggal Lahir" -> "tanggal_lahir"
            
            $nik = $row['nik'] ?? null;
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

                // Find or create subject
                $subject = Subject::firstOrCreate(
                    ['nik' => $nik],
                    [
                        'name' => $name,
                        'gender' => $gender,
                        'date_of_birth' => $dob,
                    ]
                );

                // If subject existed but basic info differed, we might minimally update it if needed.
                // Keeping it simple for now and creating a new measurement.

                $weight = $this->parseNumeric($row['berat_badan_kg'] ?? null);
                $height = $this->parseNumeric($row['tinggi_badan_cm'] ?? null);
                $lila = $this->parseNumeric($row['lila_cm'] ?? null);
                $headCirc = $this->parseNumeric($row['lingkar_kepala_cm'] ?? null);

                // Note: the template might have auto-calculated fields, but our backend will recalculate 
                // the categories and z-scores upon saving via the Observers, so we just provide the raw inputs.

                // Check if identical measurement already exists to avoid duplicates
                $existing = Measurement::where('subject_id', $subject->id)
                    ->whereDate('measurement_date', $measurementDate)
                    ->first();

                if ($existing) {
                    $this->skippedCount++;
                    continue; // Skip duplicate
                }

                Measurement::create([
                    'subject_id' => $subject->id,
                    'user_id' => auth()->id() ?? 1, // Fallback to ID 1 if no auth context
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
