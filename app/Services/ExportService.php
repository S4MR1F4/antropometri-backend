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

        return Excel::download(new MeasurementsExport($filters), $filename);
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

        $pdf = Pdf::loadView('reports.summary', [
            'stats' => $stats,
            'period' => $period,
            'filters' => $filters,
            'date' => now()->format('d F Y'),
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
