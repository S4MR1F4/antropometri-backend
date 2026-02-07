<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SyncService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * SyncController - Handles offline sync endpoints
 * Per implementation_plan.md Phase 4
 */
class SyncController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected SyncService $syncService
    ) {
    }

    /**
     * Sync batch of measurements from offline storage.
     *
     * POST /sync/measurements
     *
     * Request body:
     * {
     *   "records": [
     *     {
     *       "local_id": "uuid-123",
     *       "subject_id": 1,
     *       "measurement_date": "2026-02-07",
     *       "created_at_local": "2026-02-07T10:00:00",
     *       "hash": "AHMAD PRATAMA|2024-12-07",
     *       "weight": 9.5,
     *       "height": 75.2,
     *       "category": "balita",
     *       ...
     *     }
     *   ]
     * }
     */
    public function syncMeasurements(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'records' => 'required|array|min:1|max:100',
            'records.*.local_id' => 'required|string',
            'records.*.subject_id' => 'required|integer|exists:subjects,id',
            'records.*.measurement_date' => 'required|date',
            'records.*.created_at_local' => 'required|date',
            'records.*.hash' => 'required|string',
            'records.*.weight' => 'required|numeric|min:0.1|max:500',
            'records.*.height' => 'required|numeric|min:10|max:300',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                message: 'Data tidak valid',
                errors: $validator->errors()->toArray(),
                code: 422
            );
        }

        try {
            $result = $this->syncService->processBatch($request->input('records'));

            return $this->successResponse(
                data: $result,
                message: 'Sinkronisasi selesai'
            );
        } catch (\Throwable $e) {
            return $this->errorResponse(
                message: 'Sinkronisasi gagal: ' . $e->getMessage(),
                code: 500
            );
        }
    }
}
