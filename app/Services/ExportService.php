<?php

namespace App\Services;

use App\Exports\MeasurementsExport;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Export service for Excel and PDF.
 * Per 07_api_specification.md §5 and 09_security_access.md §7
 */
class ExportService
{
    public function __construct(
        protected StatisticsService $statisticsService
    ) {
    }

    /**
     * Export examination data to Excel.
     */
    public function exportToExcel(array $filters = [])
    {
        $filename = 'antropometri_export_' . now()->format('Ymd_His') . '.xlsx';

        // Log the activity
        ActivityLog::log(
            action: 'export_excel',
            newValues: ['filters' => $filters, 'filename' => $filename]
        );

        return Excel::download(new \App\Exports\DualSheetExport($filters), $filename);
    }

    /**
     * Export summary report to PDF.
     */
    public function exportToPdf(array $filters = [])
    {
        $stats = $this->statisticsService->getDashboardStats($filters);
        $period = $this->getPeriodString($filters);

        $filename = 'antropometri_summary_' . now()->format('Ymd_His') . '.pdf';

        // Log the activity
        ActivityLog::log(
            action: 'export_pdf',
            newValues: ['filters' => $filters, 'filename' => $filename]
        );

        // Fetch measurements for the list (with user for inputter info)
        $query = \App\Models\Measurement::query()->with(['subject', 'user']);

        if (!empty($filters['from_date'])) {
            $query->where('measurement_date', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->where('measurement_date', '<=', $filters['to_date']);
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Clone query for aggregation to avoid limit/latest ordering issues if any
        $aggQuery = clone $query;
        $allMeasurements = $aggQuery->orderBy('measurement_date', 'desc')->get();

        // 1. Gender Distribution
        $byGender = $allMeasurements->groupBy(fn($m) => $m->subject->gender ?? 'Tidak Diketahui')->map->count();

        // 2. Monthly Trend
        $monthlyTrend = $allMeasurements->groupBy(fn($m) => $m->measurement_date->format('Y-m'))->map->count()->sortKeys();

        // 3. Detailed Status Counts (Like Excel)
        $statusCounts = [
            'Gizi Buruk' => 0,
            'Gizi Kurang' => 0,
            'Gizi Baik' => 0,
            'Gizi Lebih' => 0,
            'Obesitas' => 0,
            'Sangat Pendek' => 0,
            'Pendek' => 0,
            'Normal' => 0,
            'Tinggi' => 0,
        ];

        foreach ($allMeasurements as $m) {
            $sList = [$m->status_bbu, $m->status_tbu, $m->status_bbtb, $m->status_imtu, $m->status_bmi];
            foreach ($sList as $s) {
                if (!$s)
                    continue;
                if (stripos($s, 'buruk') !== false)
                    $statusCounts['Gizi Buruk']++;
                elseif (stripos($s, 'kurang') !== false)
                    $statusCounts['Gizi Kurang']++;
                elseif (stripos($s, 'baik') !== false)
                    $statusCounts['Gizi Baik']++;
                elseif (stripos($s, 'lebih') !== false)
                    $statusCounts['Gizi Lebih']++;
                elseif (stripos($s, 'obesitas') !== false)
                    $statusCounts['Obesitas']++;
                elseif (stripos($s, 'sangat pendek') !== false)
                    $statusCounts['Sangat Pendek']++;
                elseif (stripos($s, 'pendek') !== false)
                    $statusCounts['Pendek']++;
                elseif (stripos($s, 'normal') !== false)
                    $statusCounts['Normal']++;
                elseif (stripos($s, 'tinggi') !== false)
                    $statusCounts['Tinggi']++;
            }
        }

        // Limit for the list display if needed, but if user wants full report, maybe don't limit?
        // User said "summary nya saja", but implies enriching the PDF.
        // If the dataset is huge, PDF generation might fail. 
        // I will keep the limit of 300 for the *list* to prevent OOM, or maybe 500.
        // But aggregates are on full dataset.
        $measurementsList = $allMeasurements->take(500);

        $pdf = Pdf::loadView('reports.summary', [
            'stats' => $stats,
            'period' => $period,
            'filters' => $filters,
            'date' => now()->format('d F Y'),
            'measurements' => $measurementsList,
            // New Data
            'byGender' => $byGender,
            'monthlyTrend' => $monthlyTrend,
            'statusCounts' => $statusCounts,
        ]);

        return $pdf->download($filename);
    }

    protected function getPeriodString(array $filters): string
    {
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            return "Periode: {$filters['from_date']} s/d {$filters['to_date']}";
        } elseif (!empty($filters['from_date'])) {
            return "Sejak: {$filters['from_date']}";
        } elseif (!empty($filters['to_date'])) {
            return "Hingga: {$filters['to_date']}";
        }

        return "Semua Waktu";
    }
}
