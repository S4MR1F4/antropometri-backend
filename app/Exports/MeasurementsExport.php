<?php

namespace App\Exports;

use App\Models\Measurement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Excel export logic for examination data.
 * Per 07_api_specification.md §5.3 and 06_data_dictionary.md §4
 */
class MeasurementsExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
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
        $query = Measurement::query()->with(['subject', 'user'])->whereHas('subject');

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
        $row = $this->rowNumber + 1;

        // Auto formulas for Excel interactivity
        $formulaAgeMonths = '=IF(OR(ISBLANK(C'.$row.'),ISBLANK(D'.$row.')),"",DATEDIF(C'.$row.',D'.$row.',"m"))';
        $formulaAgeYears = '=IF(ISBLANK(K'.$row.'),"",ROUND(K'.$row.'/12,1))';
        $formulaCategory = '=IF(F'.$row.'="Y","bumil",IF(K'.$row.'<=60,"balita",IF(K'.$row.'<=216,"remaja","dewasa")))';
        $formulaBmi = '=IF(OR(ISBLANK(G'.$row.'),ISBLANK(H'.$row.')),"",ROUND(G'.$row.'/((H'.$row.'/100)*(H'.$row.'/100)),2))';
        $formulaStatusBmi = '=IF(ISBLANK(V'.$row.'),"",IF(V'.$row.'<18.5,"Berat Badan Kurang",IF(V'.$row.'<=25,"Normal",IF(V'.$row.'<=27,"Kelebihan Berat Badan Ringan","Kelebihan Berat Badan Berat"))))';

        return [
            $this->rowNumber,
            $measurement->subject->name ?? '',
            $measurement->subject->date_of_birth ? $measurement->subject->date_of_birth->format('Y-m-d') : null,
            $measurement->measurement_date ? $measurement->measurement_date->format('Y-m-d') : null,
            $measurement->subject->gender ?? '',
            $measurement->is_pregnant ? 'Y' : 'T',
            $measurement->weight,
            $measurement->height,
            $measurement->arm_circumference,
            $measurement->head_circumference,
            $formulaAgeMonths,
            $formulaAgeYears,
            $formulaCategory,
            $measurement->zscore_bbu,
            $measurement->status_bbu,
            $measurement->zscore_tbu,
            $measurement->status_tbu,
            $measurement->zscore_bbtb,
            $measurement->status_bbtb,
            $measurement->zscore_imtu,
            $measurement->status_imtu,
            $formulaBmi,
            $formulaStatusBmi,
            $measurement->status_kek ?? $measurement->status_lila,
            $measurement->recommendation,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Header Styling (Emerald/Teal Theme)
        $sheet->getStyle('A1:Y1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
                'name' => 'Segoe UI',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D9488'],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // 2. Data Row Styling & AutoFilter
        $lastRow = max(2, $this->rowNumber + 1);
        $sheet->setAutoFilter('A1:Y' . $lastRow);

        $sheet->getStyle('A2:Y' . $lastRow)->applyFromArray([
            'font' => [
                'size' => 10,
                'name' => 'Segoe UI',
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Alignment adjustments
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:F' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K2:M' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
