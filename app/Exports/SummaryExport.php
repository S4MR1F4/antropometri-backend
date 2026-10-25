<?php

namespace App\Exports;

use App\Models\Measurement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SummaryExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithCharts, WithEvents
{
    protected array $filters;

    // Track table indices for charts and styling
    private int $metricsHeaderRow = 6;
    private int $metricsStartRow = 7;
    private int $metricsEndRow = 9;

    private int $categoryHeaderRow = 0;
    private int $categoryStartRow = 0;
    private int $categoryCount = 0;
    private int $categoryTotalRow = 0;

    private int $trendHeaderRow = 0;
    private int $trendStartRow = 0;
    private int $trendCount = 0;
    private int $trendTotalRow = 0;

    private int $statusHeaderRow = 0;
    private int $statusStartRow = 0;
    private int $statusCount = 0;
    private int $statusTotalRow = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // 1. Fetch Data
        $query = Measurement::query()->with('subject')->whereHas('subject');

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

        $measurements = $query->latest('measurement_date')->get();

        // 2. Prepare Aggregates
        $total = $measurements->count();
        $byCategory = $measurements->groupBy('category')->map(fn($group) => $group->count());
        $byGender = $measurements->groupBy(fn($m) => $m->subject->gender ?? 'Tidak Diketahui')->map(fn($group) => $group->count());
        $monthlyTrend = $measurements->filter(fn($m) => !empty($m->measurement_date))
            ->groupBy(fn($m) => \Illuminate\Support\Carbon::parse($m->measurement_date)->format('Y-m'))
            ->map(fn($group) => $group->count())
            ->sortKeys();

        $statusCounts = [
            'Sangat Kurus / Gizi Buruk' => 0,
            'Kurus / Gizi Kurang' => 0,
            'Normal / Gizi Baik' => 0,
            'Gemuk / Gizi Lebih' => 0,
            'Obesitas' => 0,
            'Sangat Pendek' => 0,
            'Pendek' => 0,
            'Tinggi' => 0,
        ];

        foreach ($measurements as $m) {
            $sList = [$m->status_bbu, $m->status_tbu, $m->status_bbtb, $m->status_imtu, $m->status_bmi];
            foreach ($sList as $s) {
                if (!$s) continue;
                $lowS = strtolower($s);

                if (str_contains($lowS, 'buruk') || str_contains($lowS, 'tingkat berat') || str_contains($lowS, 'sangat kurus'))
                    $statusCounts['Sangat Kurus / Gizi Buruk']++;
                elseif (str_contains($lowS, 'kurang') || str_contains($lowS, 'tingkat ringan') || (str_contains($lowS, 'kurus') && !str_contains($lowS, 'sangat')))
                    $statusCounts['Kurus / Gizi Kurang']++;
                elseif (str_contains($lowS, 'baik') || str_contains($lowS, 'normal'))
                    $statusCounts['Normal / Gizi Baik']++;
                elseif (str_contains($lowS, 'lebih') || str_contains($lowS, 'gemuk'))
                    $statusCounts['Gemuk / Gizi Lebih']++;
                elseif (str_contains($lowS, 'obesitas'))
                    $statusCounts['Obesitas']++;
                elseif (str_contains($lowS, 'sangat pendek'))
                    $statusCounts['Sangat Pendek']++;
                elseif (str_contains($lowS, 'pendek'))
                    $statusCounts['Pendek']++;
                elseif (str_contains($lowS, 'tinggi'))
                    $statusCounts['Tinggi']++;
            }
        }

        $totalStatusFound = array_sum($statusCounts);

        // 3. Build Rows and Track Row Indices
        $rows = [];
        $currentRow = 1;

        // Title & Metadata
        $rows[] = ['LAPORAN RINGKASAN & ANALISIS DATA ANTROPOMETRI'];
        $currentRow++; // 2

        $rows[] = ['Waktu Ekspor', now()->format('d F Y H:i:s')];
        $currentRow++; // 3

        $periodStr = (!empty($this->filters['from_date']) && !empty($this->filters['to_date']))
            ? "{$this->filters['from_date']} s/d {$this->filters['to_date']}"
            : ((!empty($this->filters['from_date'])) ? "Sejak {$this->filters['from_date']}" : ((!empty($this->filters['to_date'])) ? "Hingga {$this->filters['to_date']}" : "Semua Waktu (Seluruh Data)"));

        $rows[] = ['Periode Pemeriksaan', $periodStr];
        $currentRow++; // 4

        $rows[] = ['Kategori Pasien', ucfirst($this->filters['category'] ?? 'Semua Kategori')];
        $currentRow++; // 5

        $rows[] = [''];
        $currentRow++; // 6

        // 1. Metrics Summary Table
        $this->metricsHeaderRow = $currentRow;
        $rows[] = ['RINGKASAN METRIK UTAMA', 'JUMLAH', 'PERSENTASE'];
        $currentRow++;

        $this->metricsStartRow = $currentRow;
        $rows[] = ['Total Pemeriksaan', $total, $total > 0 ? '100.0%' : '0.0%'];
        $currentRow++;

        $rows[] = [
            'Total Laki-Laki',
            $byGender['L'] ?? 0,
            $total > 0 ? round((($byGender['L'] ?? 0) / $total) * 100, 1) . '%' : '0.0%'
        ];
        $currentRow++;

        $rows[] = [
            'Total Perempuan',
            $byGender['P'] ?? 0,
            $total > 0 ? round((($byGender['P'] ?? 0) / $total) * 100, 1) . '%' : '0.0%'
        ];
        $this->metricsEndRow = $currentRow;
        $currentRow++;

        $rows[] = [''];
        $currentRow++;

        // 2. Category Distribution Table
        $this->categoryHeaderRow = $currentRow;
        $rows[] = ['DISTRIBUSI KATEGORI USIA', 'JUMLAH', 'PERSENTASE'];
        $currentRow++;

        $this->categoryStartRow = $currentRow;
        $this->categoryCount = 0;
        foreach ($byCategory as $cat => $count) {
            $pct = $total > 0 ? round(($count / $total) * 100, 1) . '%' : '0.0%';
            $rows[] = [ucfirst($cat ?? 'Lainnya'), $count, $pct];
            $currentRow++;
            $this->categoryCount++;
        }

        $this->categoryTotalRow = $currentRow;
        $rows[] = ['TOTAL PASIEN', $total, '100.0%'];
        $currentRow++;

        $rows[] = [''];
        $currentRow++;

        // 3. Monthly Trend Table
        $this->trendHeaderRow = $currentRow;
        $rows[] = ['TREN PEMERIKSAAN BULANAN', 'JUMLAH'];
        $currentRow++;

        $this->trendStartRow = $currentRow;
        $this->trendCount = 0;
        foreach ($monthlyTrend as $month => $count) {
            $dateObj = \DateTime::createFromFormat('!Y-m', $month);
            $rows[] = [$dateObj ? $dateObj->format('F Y') : $month, $count];
            $currentRow++;
            $this->trendCount++;
        }

        $this->trendTotalRow = $currentRow;
        $rows[] = ['TOTAL PEMERIKSAAN', $total];
        $currentRow++;

        $rows[] = [''];
        $currentRow++;

        // 4. Nutritional Status Table
        $this->statusHeaderRow = $currentRow;
        $rows[] = ['INDIKATOR STATUS GIZI', 'FREKUENSI', 'PERSENTASE'];
        $currentRow++;

        $this->statusStartRow = $currentRow;
        $this->statusCount = 0;
        foreach ($statusCounts as $status => $count) {
            $pct = $totalStatusFound > 0 ? round(($count / $totalStatusFound) * 100, 1) . '%' : '0.0%';
            $rows[] = [$status, $count, $pct];
            $currentRow++;
            $this->statusCount++;
        }

        $this->statusTotalRow = $currentRow;
        $rows[] = ['TOTAL TEMUAN STATUS', $totalStatusFound, '100.0%'];

        return new Collection($rows);
    }

    public function charts(): array
    {
        $charts = [];
        $nextChartRow = 6;

        // 1. Category Pie Chart
        if ($this->categoryCount > 0) {
            $labelRange = "'Ringkasan & Grafik'!\$A\${$this->categoryStartRow}:\$A\$" . ($this->categoryStartRow + $this->categoryCount - 1);
            $valueRange = "'Ringkasan & Grafik'!\$B\${$this->categoryStartRow}:\$B\$" . ($this->categoryStartRow + $this->categoryCount - 1);

            $labels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $labelRange, null, $this->categoryCount)];
            $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $valueRange, null, $this->categoryCount)];

            $series = new DataSeries(
                DataSeries::TYPE_PIECHART,
                null,
                range(0, count($values) - 1),
                [],
                $labels,
                $values
            );

            $layout = new \PhpOffice\PhpSpreadsheet\Chart\Layout();
            $layout->setShowVal(true);
            $layout->setShowPercent(true);

            $plot = new PlotArea($layout, [$series]);
            $legend = new Legend(Legend::POSITION_RIGHT, null, false);
            $title = new Title('Distribusi Kategori Pasien');

            $chart = new Chart('chart_category', $title, $legend, $plot);
            $chart->setTopLeftPosition('F' . $nextChartRow);
            $chart->setBottomRightPosition('O' . ($nextChartRow + 14));
            $nextChartRow += 17; // 15 rows height + 2 gap rows
            $charts[] = $chart;
        }

        // 2. Trend Bar/Column Chart
        if ($this->trendCount > 0) {
            $labelRange = "'Ringkasan & Grafik'!\$A\${$this->trendStartRow}:\$A\$" . ($this->trendStartRow + $this->trendCount - 1);
            $valueRange = "'Ringkasan & Grafik'!\$B\${$this->trendStartRow}:\$B\$" . ($this->trendStartRow + $this->trendCount - 1);

            $labels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $labelRange, null, $this->trendCount)];
            $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $valueRange, null, $this->trendCount)];

            $series = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($values) - 1),
                [],
                $labels,
                $values
            );
            $series->setPlotDirection(DataSeries::DIRECTION_COL);

            $plot = new PlotArea(null, [$series]);
            $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
            $title = new Title('Tren Jumlah Pemeriksaan Bulanan');

            $chart = new Chart('chart_trend', $title, $legend, $plot);
            $chart->setTopLeftPosition('F' . $nextChartRow);
            $chart->setBottomRightPosition('O' . ($nextChartRow + 14));
            $nextChartRow += 17;
            $charts[] = $chart;
        }

        // 3. Status Bar/Column Chart
        if ($this->statusCount > 0) {
            $labelRange = "'Ringkasan & Grafik'!\$A\${$this->statusStartRow}:\$A\$" . ($this->statusStartRow + $this->statusCount - 1);
            $valueRange = "'Ringkasan & Grafik'!\$B\${$this->statusStartRow}:\$B\$" . ($this->statusStartRow + $this->statusCount - 1);

            $labels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $labelRange, null, $this->statusCount)];
            $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $valueRange, null, $this->statusCount)];

            $series = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($values) - 1),
                [],
                $labels,
                $values
            );
            $series->setPlotDirection(DataSeries::DIRECTION_COL);

            $plot = new PlotArea(null, [$series]);
            $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
            $title = new Title('Distribusi Status Gizi & Pertumbuhan');

            $chart = new Chart('chart_status', $title, $legend, $plot);
            $chart->setTopLeftPosition('F' . $nextChartRow);
            $chart->setBottomRightPosition('O' . ($nextChartRow + 18));
            $nextChartRow += 21;
            $charts[] = $chart;
        }

        return $charts;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Ringkasan & Grafik';
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Column Widths
                $sheet->getColumnDimension('A')->setWidth(35);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(4);
                $sheet->getColumnDimension('E')->setWidth(3);
                foreach (range('F', 'O') as $col) {
                    $sheet->getColumnDimension($col)->setWidth(11);
                }

                // 2. Title & Metadata Styling
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'name' => 'Segoe UI',
                        'color' => ['rgb' => '0F766E'],
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                $sheet->getStyle('A2:A4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'name' => 'Segoe UI',
                        'color' => ['rgb' => '334155'],
                    ],
                ]);

                $sheet->getStyle('B2:B4')->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'name' => 'Segoe UI',
                        'color' => ['rgb' => '475569'],
                    ],
                ]);

                // Helper closures for styling tables
                $styleHeader = function (int $row, string $bgColor = '0F766E', string $lastCol = 'C') use ($sheet) {
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 10,
                            'name' => 'Segoe UI',
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $bgColor],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getStyle("B{$row}:{$lastCol}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getRowDimension($row)->setRowHeight(26);
                };

                $styleRows = function (int $startRow, int $endRow, string $lastCol = 'C') use ($sheet) {
                    if ($startRow > $endRow) return;
                    for ($r = $startRow; $r <= $endRow; $r++) {
                        $isZebra = ($r % 2 === 1);
                        if ($isZebra) {
                            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getFill()->applyFromArray([
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'],
                            ]);
                        }
                        $sheet->getRowDimension($r)->setRowHeight(20);
                    }

                    $sheet->getStyle("A{$startRow}:{$lastCol}{$endRow}")->applyFromArray([
                        'font' => [
                            'size' => 10,
                            'name' => 'Segoe UI',
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E2E8F0'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $sheet->getStyle("B{$startRow}:B{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    if ($lastCol === 'C') {
                        $sheet->getStyle("C{$startRow}:C{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                };

                $styleTotalRow = function (int $row, string $lastCol = 'C') use ($sheet) {
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 10,
                            'name' => 'Segoe UI',
                            'color' => ['rgb' => '0F172A'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F1F5F9'],
                        ],
                        'borders' => [
                            'top' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '94A3B8'],
                            ],
                            'bottom' => [
                                'borderStyle' => Border::BORDER_DOUBLE,
                                'color' => ['rgb' => '64748B'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    if ($lastCol === 'C') {
                        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                    $sheet->getRowDimension($row)->setRowHeight(22);
                };

                // 3. Style Metrics Table
                $styleHeader($this->metricsHeaderRow, '0F766E', 'C');
                $styleRows($this->metricsStartRow, $this->metricsEndRow, 'C');

                // 4. Style Category Table
                if ($this->categoryCount > 0) {
                    $styleHeader($this->categoryHeaderRow, '0D9488', 'C');
                    $styleRows($this->categoryStartRow, $this->categoryStartRow + $this->categoryCount - 1, 'C');
                    $styleTotalRow($this->categoryTotalRow, 'C');
                }

                // 5. Style Trend Table
                if ($this->trendCount > 0) {
                    $styleHeader($this->trendHeaderRow, '1E293B', 'B');
                    $styleRows($this->trendStartRow, $this->trendStartRow + $this->trendCount - 1, 'B');
                    $styleTotalRow($this->trendTotalRow, 'B');
                }

                // 6. Style Status Table
                if ($this->statusCount > 0) {
                    $styleHeader($this->statusHeaderRow, '0F766E', 'C');
                    $styleRows($this->statusStartRow, $this->statusStartRow + $this->statusCount - 1, 'C');
                    $styleTotalRow($this->statusTotalRow, 'C');
                }
            },
        ];
    }
}
