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
     */
    public function importMeasurements(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas unggahan tidak valid atau corrupt.',
            ], 422);
        }

        // 1. Strict extension whitelist
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            return response()->json([
                'success' => false,
                'message' => 'Format berkas tidak diizinkan. Hanya berkas .xlsx, .xls, atau .csv yang didukung.',
            ], 422);
        }

        // 2. Anti-php2shell / Anti-RCE filename check
        $originalName = $file->getClientOriginalName();
        if (
            preg_match('/\.(php|phtml|phar|pl|py|cgi|sh|bash|exe|bat|cmd|dll|inc|jsp|asp)\b/i', $originalName) ||
            str_contains($originalName, "\0") ||
            str_contains($originalName, '..')
        ) {
            Log::critical('Security: Malicious filename blocked in import.', [
                'user_id' => $request->user()?->id,
                'filename' => $originalName,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nama berkas tidak aman atau terdeteksi ancaman eksekusi skrip.',
            ], 422);
        }

        try {
            $import = new MeasurementsImport();
            Excel::import($import, $file);

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
