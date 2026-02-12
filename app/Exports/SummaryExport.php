<?php

namespace App\Exports;

use App\Models\Measurement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithCharts;
use PhpOffice\PhpSpreadsheet\CustomProperties;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class SummaryExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithCharts
{
    protected array $filters;

    // Store data ranges for chart generation
    private int $categoryStartRow = 0;
    private int $categoryCount = 0;

    private int $trendStartRow = 0;
    private int $trendCount = 0;

    private int $statusStartRow = 0;
    private int $statusCount = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // 1. Fetch Data
        $query = Measurement::query()->with('subject');

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
                if (!$s)
                    continue;
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

        // 3. Build Rows and Track Indices
        // Note: rows are 1-indexed in Excel.
        // But Collection is 0-indexed. When exported, index 0 becomes Row 1.
        $rows = [];
        $currentRow = 1;

        // Header
        $rows[] = ['LAPORAN RINGKASAN & ANALISIS DATA'];
        $currentRow++;
        $rows[] = ['Tanggal Export', now()->format('d F Y H:i')];
        $currentRow++;
        $rows[] = ['Periode Filter', ($this->filters['from_date'] ?? 'Awal') . ' s/d ' . ($this->filters['to_date'] ?? 'Akhir')];
        $currentRow++;
        $rows[] = ['Kategori Filter', ucfirst($this->filters['category'] ?? 'Semua')];
        $currentRow++;
        $rows[] = [''];
        $currentRow++;

        // Summary Stats
        $rows[] = ['RINGKASAN UTAMA'];
        $currentRow++;
        $rows[] = ['Total Pemeriksaan', $total];
        $currentRow++;
        $rows[] = ['Total Laki-Laki', $byGender['L'] ?? 0];
        $currentRow++;
        $rows[] = ['Total Perempuan', $byGender['P'] ?? 0];
        $currentRow++;
        $rows[] = [''];
        $currentRow++;

        // Category Table
        $rows[] = ['DISTRIBUSI BERDASARKAN KATEGORI USIA'];
        $currentRow++;
        $rows[] = ['Kategori', 'Jumlah'];
        $currentRow++;

        $this->categoryStartRow = $currentRow; // First data row
        $this->categoryCount = 0;
        foreach ($byCategory as $cat => $count) {
            $rows[] = [ucfirst($cat ?? 'Lainnya'), $count];
            $currentRow++;
            $this->categoryCount++;
        }
        $rows[] = [''];
        $currentRow++;

        // Trend Table
        $rows[] = ['TREN PEMERIKSAAN BULANAN'];
        $currentRow++;
        $rows[] = ['Bulan', 'Jumlah Pemeriksaan'];
        $currentRow++;

        $this->trendStartRow = $currentRow;
        $this->trendCount = 0;
        foreach ($monthlyTrend as $month => $count) {
            $dateObj = \DateTime::createFromFormat('!Y-m', $month);
            $rows[] = [$dateObj ? $dateObj->format('F Y') : $month, $count];
            $currentRow++;
            $this->trendCount++;
        }
        $rows[] = [''];
        $currentRow++;

        // Status Table
        $rows[] = ['REKAPITULASI STATUS GIZI & PERTUMBUHAN'];
        $currentRow++;
        $rows[] = ['Indikator Status', 'Frekuensi Kemunculan'];
        $currentRow++;

        $this->statusStartRow = $currentRow;
        $this->statusCount = 0;
        foreach ($statusCounts as $status => $count) {
            $rows[] = [$status, $count];
            $currentRow++;
            $this->statusCount++;
        }

        return new Collection($rows);
    }

    public function charts(): array
    {
        $charts = [];

        // 1. Category Pie Chart
        if ($this->categoryCount > 0) {
            $labelRange = 'Ringkasan & Grafik!$A$' . $this->categoryStartRow . ':$A$' . ($this->categoryStartRow + $this->categoryCount - 1);
            $valueRange = 'Ringkasan & Grafik!$B$' . $this->categoryStartRow . ':$B$' . ($this->categoryStartRow + $this->categoryCount - 1);

            $labels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $labelRange, null, $this->categoryCount)];
            $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $valueRange, null, $this->categoryCount)];

            $series = new DataSeries(
                DataSeries::TYPE_PIECHART,
                null,
                range(0, count($values) - 1),
                [], // Plot Labels
                $labels, // Plot Categories
                $values // Plot Values
            );
            // Pie charts don't use grouping/direction in same way, but let's be minimal

            $layout = new \PhpOffice\PhpSpreadsheet\Chart\Layout();
            $layout->setShowVal(true);
            $layout->setShowPercent(true);

            $plot = new PlotArea($layout, [$series]);
            $legend = new Legend(Legend::POSITION_RIGHT, null, false);
            $title = new Title('Distribusi Kategori');

            $chart = new Chart('chart_category', $title, $legend, $plot);
            $chart->setTopLeftPosition('E' . ($this->categoryStartRow - 2));
            $chart->setBottomRightPosition('L' . ($this->categoryStartRow + 12));

            $charts[] = $chart;
        }

        // 2. Trend Bar Chart
        if ($this->trendCount > 0) {
            $labelRange = 'Ringkasan & Grafik!$A$' . $this->trendStartRow . ':$A$' . ($this->trendStartRow + $this->trendCount - 1);
            $valueRange = 'Ringkasan & Grafik!$B$' . $this->trendStartRow . ':$B$' . ($this->trendStartRow + $this->trendCount - 1);

            $labels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $labelRange, null, $this->trendCount)];
            $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $valueRange, null, $this->trendCount)];

            $series = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($values) - 1),
                [], // Plot Labels
                $labels, // Plot Categories
                $values // Plot Values
            );
            $series->setPlotDirection(DataSeries::DIRECTION_COL);

            $plot = new PlotArea(null, [$series]);
            $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
            $title = new Title('Tren Bulanan');

            $chart = new Chart('chart_trend', $title, $legend, $plot);
            $chart->setTopLeftPosition('E' . ($this->trendStartRow - 2));
            $chart->setBottomRightPosition('M' . ($this->trendStartRow + 12));

            $charts[] = $chart;
        }

        // 3. Status Bar Chart
        if ($this->statusCount > 0) {
            $labelRange = 'Ringkasan & Grafik!$A$' . $this->statusStartRow . ':$A$' . ($this->statusStartRow + $this->statusCount - 1);
            $valueRange = 'Ringkasan & Grafik!$B$' . $this->statusStartRow . ':$B$' . ($this->statusStartRow + $this->statusCount - 1);

            $labels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $labelRange, null, $this->statusCount)];
            $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $valueRange, null, $this->statusCount)];

            $series = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($values) - 1),
                [], // Plot Labels
                $labels, // Plot Categories
                $values // Plot Values
            );
            $series->setPlotDirection(DataSeries::DIRECTION_COL);

            $plot = new PlotArea(null, [$series]);
            $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
            $title = new Title('Distribusi Status Gizi');

            $chart = new Chart('chart_status', $title, $legend, $plot);
            $chart->setTopLeftPosition('E' . ($this->statusStartRow - 2));
            $chart->setBottomRightPosition('N' . ($this->statusStartRow + 15));

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
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            6 => ['font' => ['bold' => true]],
        ]; // Simplification, other bold rows will be dynamically styled if I knew their indices, 
        // but since indices shift, I am skipping dynamic styling for now to focus on charts.
    }
}
