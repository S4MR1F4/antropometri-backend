<?php

namespace App\Exports;

use App\Models\Measurement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Excel export logic for examination data.
 * Per 07_api_specification.md §5.3 and 06_data_dictionary.md §4
 */
class MeasurementsExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    public function title(): string
    {
        return 'Data Detail (Raw)';
    }

    protected array $filters;
    private int $rowNumber = 0;

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
        // 1. Header Styling (Dark Teal / Emerald Theme)
        $sheet->getStyle('A1:Y1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
                'name' => 'Segoe UI',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F766E'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        // 2. Data Row Styling & AutoFilter
        $lastRow = max(2, $this->rowNumber + 1);
        $sheet->setAutoFilter('A1:Y' . $lastRow);

        $sheet->getStyle('A2:Y' . $lastRow)->applyFromArray([
            'font' => [
                'size' => 10,
                'name' => 'Segoe UI',
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Alignment adjustments
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K2:M' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Numeric alignment
        $sheet->getStyle('G2:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('N2:N' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('P2:P' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('R2:R' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('T2:T' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('V2:V' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = max(2, $this->rowNumber + 1);

                // Freeze top row for smooth scrolling
                $sheet->freezePane('A2');

                if ($this->rowNumber > 0) {
                    // 1. Zebra striping & Row Heights
                    for ($r = 2; $r <= $lastRow; $r++) {
                        if ($r % 2 === 1) {
                            $sheet->getStyle("A{$r}:Y{$r}")->getFill()->applyFromArray([
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'],
                            ]);
                        }
                        $sheet->getRowDimension($r)->setRowHeight(20);
                    }

                    // 2. Subtle grid borders for data cells
                    $sheet->getStyle("A1:Y{$lastRow}")->getBorders()->getAllBorders()->applyFromArray([
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ]);

                    // 3. Summary & Calculations Row
                    $avgRow = $lastRow + 2;
                    $sheet->setCellValue("B{$avgRow}", "RATA-RATA KESELURUHAN");
                    $sheet->setCellValue("G{$avgRow}", "=AVERAGE(G2:G{$lastRow})");
                    $sheet->setCellValue("H{$avgRow}", "=AVERAGE(H2:H{$lastRow})");
                    $sheet->setCellValue("I{$avgRow}", "=AVERAGE(I2:I{$lastRow})");
                    $sheet->setCellValue("J{$avgRow}", "=AVERAGE(J2:J{$lastRow})");
                    $sheet->setCellValue("K{$avgRow}", "=AVERAGE(K2:K{$lastRow})");
                    $sheet->setCellValue("L{$avgRow}", "=AVERAGE(L2:L{$lastRow})");
                    $sheet->setCellValue("V{$avgRow}", "=AVERAGE(V2:V{$lastRow})");

                    $totalRow = $avgRow + 1;
                    $sheet->setCellValue("B{$totalRow}", "TOTAL PEMERIKSAAN");
                    $sheet->setCellValue("G{$totalRow}", "=COUNTA(A2:A{$lastRow})");

                    // Number formats
                    $sheet->getStyle("G{$avgRow}:L{$avgRow}")->getNumberFormat()->setFormatCode('0.00');
                    $sheet->getStyle("V{$avgRow}")->getNumberFormat()->setFormatCode('0.00');
                    $sheet->getStyle("G{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');

                    // Style Summary Block
                    $sheet->getStyle("A{$avgRow}:Y{$totalRow}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'name' => 'Segoe UI',
                            'size' => 10,
                            'color' => ['rgb' => '0F172A'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F1F5F9'],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $sheet->getStyle("G{$avgRow}:L{$avgRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("V{$avgRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("G{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $sheet->getStyle("A{$avgRow}:Y{$avgRow}")->getBorders()->getTop()->applyFromArray([
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '94A3B8'],
                    ]);

                    $sheet->getStyle("A{$totalRow}:Y{$totalRow}")->getBorders()->getBottom()->applyFromArray([
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '64748B'],
                    ]);

                    $sheet->getRowDimension($avgRow)->setRowHeight(24);
                    $sheet->getRowDimension($totalRow)->setRowHeight(24);
                }
            },
        ];
    }
}
