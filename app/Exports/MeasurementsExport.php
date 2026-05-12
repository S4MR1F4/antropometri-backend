<?php

namespace App\Exports;

use App\Models\Measurement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Excel export logic for examination data.
 * Per 07_api_specification.md §5.3 and 06_data_dictionary.md §4
 */
class MeasurementsExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function title(): string
    {
        return 'Data Detail (Raw)';
    }

    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Measurement::query()->with(['subject', 'user']);

        if (!empty($this->filters['from_date'])) {
            $query->where('measurement_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->where('measurement_date', '<=', $this->filters['to_date']);
        }

        if (!empty($this->filters['category'])) {
            $query->where('category', $this->filters['category']);
        }

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        return $query->latest('measurement_date');
    }

    private int $rowNumber = 0;

    public function headings(): array
    {
        return [
            'No',
            'Nama Pasien',
            'Tanggal Lahir',
            'Tanggal Periksa',
            'Jenis Kelamin (L/P)',
            'Hamil? (Y/T)',
            'Berat Badan (kg)',
            'Tinggi Badan (cm)',
            'LILA (cm)',
            'Lingkar Kepala (cm)',
            'Umur (Bulan) [Auto]',
            'Umur (Tahun) [Auto]',
            'Kategori [Auto]',
            'Z-Score BB/U [Auto]',
            'Status BB/U [Auto]',
            'Z-Score TB/U [Auto]',
            'Status TB/U [Auto]',
            'Z-Score BB/TB [Auto]',
            'Status BB/TB [Auto]',
            'Z-Score IMT/U (Remaja) [Auto]',
            'Status IMT/U (Remaja) [Auto]',
            'IMT Dewasa [Auto]',
            'Status IMT Dewasa [Auto]',
            'Status LILA (Bumil) [Auto]',
            'Rekomendasi [Auto]',
        ];
    }

    public function map($measurement): array
    {
        $this->rowNumber++;
        $ageInYears = is_numeric($measurement->age_in_months) ? round($measurement->age_in_months / 12, 1) : '-';
        
        return [
            $this->rowNumber,
            $measurement->subject->name ?? '-',
            $measurement->subject->date_of_birth ? $measurement->subject->date_of_birth->format('Y-m-d') : '-',
            $measurement->measurement_date ? $measurement->measurement_date->format('Y-m-d') : '-',
            $measurement->subject->gender ?? '-',
            $measurement->is_pregnant ? 'Y' : 'T',
            $measurement->weight,
            $measurement->height,
            $measurement->arm_circumference,
            $measurement->head_circumference,
            $measurement->age_in_months,
            $ageInYears,
            $measurement->category,
            $measurement->zscore_bbu,
            $measurement->status_bbu,
            $measurement->zscore_tbu,
            $measurement->status_tbu,
            $measurement->zscore_bbtb,
            $measurement->status_bbtb,
            $measurement->zscore_imtu,
            $measurement->status_imtu,
            $measurement->bmi,
            $measurement->status_bmi,
            $measurement->status_kek ?? $measurement->status_lila,
            $measurement->recommendation,
        ];
    }
}
