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
            $row = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array) $row);
            // Mapping based on template headers (slugified by WithHeadingRow)
            // Example: "Nama Pasien" -> "nama_pasien", "Tanggal Lahir" -> "tanggal_lahir"
            
            $name = $this->getValue($row, ['nama_pasien', 'nama_subjek', 'nama', 'patient_name', 'name', 'subject_name']);

            // Skip empty rows, template filler rows, or summary calculation rows
            $upperName = strtoupper(trim((string)$name));
            if (
                empty($name) ||
                $upperName === '' ||
                $upperName === 'LENGKAPI DATA!' ||
                str_starts_with($upperName, 'TOTAL') ||
                str_starts_with($upperName, 'RATA-RATA') ||
                str_starts_with($upperName, 'RINGKASAN')
            ) {
                $this->skippedCount++;
                continue;
            }

            try {
                // Parse dates with aliases
                $dobRaw = $this->getValue($row, ['tanggal_lahir', 'tgl_lahir', 'tgl_lhr', 'date_of_birth', 'birth_date', 'dob']);
                $measuredAtRaw = $this->getValue($row, ['tanggal_periksa', 'tgl_pengukuran', 'tgl_ukur', 'tanggal_ukur', 'measurement_date', 'measured_at', 'date']);

                $dob = $this->parseDate($dobRaw);
                $measurementDate = $this->parseDate($measuredAtRaw);

                if (!$dob || !$measurementDate || !$name) {
                    $this->skippedCount++;
                    continue;
                }

                $genderRaw = $this->getValue($row, ['jenis_kelamin_lp', 'jenis_kelamin', 'subject_gender', 'gender', 'sex', 'jk'], 'L');
                $genderStr = strtoupper(trim((string)$genderRaw));
                $gender = ($genderStr === 'P' || str_starts_with($genderStr, 'P') || str_contains($genderStr, 'WANITA') || str_contains($genderStr, 'PEREMPUAN') || $genderStr === 'F') ? 'P' : 'L';

                $pregnantRaw = $this->getValue($row, ['hamil_yt', 'hamil', 'is_pregnant', 'pregnant'], 'T');
                $isPregnant = in_array(strtoupper(trim((string)$pregnantRaw)), ['Y', '1', 'TRUE', 'YA', 'IYAH', 'HAMIL']);

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

                $weight = $this->parseNumeric($this->getValue($row, ['berat_badan_kg', 'berat_kg', 'berat', 'bb', 'weight']));
                $height = $this->parseNumeric($this->getValue($row, ['tinggi_badan_cm', 'tinggi_cm', 'tinggi', 'tb', 'height']));
                $lila = $this->parseNumeric($this->getValue($row, ['lila_cm', 'lila', 'lingkar_lengan', 'arm_circumference']));
                $headCirc = $this->parseNumeric($this->getValue($row, ['lingkar_kepala_cm', 'lingkar_kepala', 'head_circumference', 'lk']));
                $waistCirc = $this->parseNumeric($this->getValue($row, ['lingkar_perut_cm', 'lingkar_perut', 'waist_circumference', 'lp']));

                // Biological range validation
                if ($weight !== null && ($weight < 0.5 || $weight > 350)) {
                    throw new \InvalidArgumentException("Berat badan ($weight kg) di luar rentang valid.");
                }
                if ($height !== null && ($height < 20 || $height > 250)) {
                    throw new \InvalidArgumentException("Tinggi badan ($height cm) di luar rentang valid.");
                }
                if ($lila !== null && ($lila < 5 || $lila > 70)) {
                    throw new \InvalidArgumentException("LILA ($lila cm) di luar rentang valid.");
                }
                if ($headCirc !== null && ($headCirc < 20 || $headCirc > 70)) {
                    throw new \InvalidArgumentException("Lingkar kepala ($headCirc cm) di luar rentang valid.");
                }
                if ($waistCirc !== null && ($waistCirc < 20 || $waistCirc > 200)) {
                    throw new \InvalidArgumentException("Lingkar perut ($waistCirc cm) di luar rentang valid.");
                }

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
                    'waist_circumference' => $waistCirc,
                    'is_pregnant' => $isPregnant,
                ]);

                $this->importedCount++;

            } catch (\Exception $e) {
                Log::error('Import error row: ' . json_encode($row) . ' Error: ' . $e->getMessage());
                $this->skippedCount++;
            }
        }
    }

    private function getValue($row, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== '') {
                return $row[$key];
            }
        }
        return $default;
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
        if (empty($value) && $value !== 0 && $value !== '0') return null;
        if (is_numeric($value)) return (float) $value;
        $val = str_replace(',', '.', (string)$value);
        return is_numeric($val) ? (float) $val : null;
    }
}
