<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\MeasurementsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    /**
     * Import measurements from Excel template.
     * Admin only.
     */
    public function importMeasurements(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $import = new MeasurementsImport();
            Excel::import($import, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Proses import selesai.',
                'data' => [
                    'imported_count' => $import->importedCount,
                    'skipped_count' => $import->skippedCount,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan import data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
