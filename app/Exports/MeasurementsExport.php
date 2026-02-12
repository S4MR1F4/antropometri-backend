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

    public function headings(): array
    {
        return [
            'ID Pengukuran',
            'Waktu Input',
            'Petugas Input',
            'ID Subjek',
            'Nama Subjek',
            'NIK',
            'Jenis Kelamin',
            'Tgl Lahir',
            'Tgl Pengukuran',
            'Kategori',
            'Usia (Bulan)',
            'Berat (kg)',
            'Tinggi (cm)',
            'Li. Kepala (cm)',
            'Li. Perut (cm)',
            'BMI',
            'Z-Score BB/U',
            'Z-Score TB/U',
            'Z-Score BB/TB',
            'Z-Score IMT/U',
            'Status BB/U',
            'Status TB/U',
            'Status BB/TB',
            'Status IMT/U',
            'Status BMI',
            'Obesitas Sentral',
            'Rekomendasi',
            'Tindak Lanjut / Catatan',
        ];
    }

    public function map($measurement): array
    {
        return [
            $measurement->id,
            $measurement->created_at ? $measurement->created_at->format('Y-m-d H:i:s') : '-',
            $measurement->user->name ?? 'Sistem',
            $measurement->subject_id,
            $measurement->subject->name ?? '-',
            $measurement->subject->nik ?? '-',
            $measurement->subject->gender ?? '-',
            $measurement->subject->date_of_birth ? $measurement->subject->date_of_birth->format('Y-m-d') : '-',
            $measurement->measurement_date->format('Y-m-d'),
            $measurement->category,
            $measurement->age_in_months,
            $measurement->weight,
            $measurement->height,
            $measurement->head_circumference,
            $measurement->waist_circumference,
            $measurement->bmi,
            $measurement->zscore_bbu,
            $measurement->zscore_tbu,
            $measurement->zscore_bbtb,
            $measurement->zscore_imtu,
            $measurement->status_bbu,
            $measurement->status_tbu,
            $measurement->status_bbtb,
            $measurement->status_imtu,
            $measurement->status_bmi,
            $measurement->has_central_obesity ? 'Ya' : 'Tidak',
            $measurement->recommendation,
            $measurement->notes,
        ];
    }
}
