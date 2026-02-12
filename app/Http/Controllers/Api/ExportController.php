<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExportService;
use Illuminate\Http\Request;

/**
 * ExportController per 07_api_specification.md §5
 */
class ExportController extends Controller
{
    public function __construct(
        protected ExportService $exportService
    ) {
    }

    /**
     * Export examination data to Excel.
     * GET /admin/export/excel
     */
    public function exportExcel(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'category']);

        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $filters['user_id'] = $user->id;
        }

        return $this->exportService->exportToExcel($filters);
    }

    /**
     * Export summary report to PDF.
     * GET /admin/export/pdf
     */
    public function exportPdf(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'category']);

        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $filters['user_id'] = $user->id;
        }

        return $this->exportService->exportToPdf($filters);
    }
}
