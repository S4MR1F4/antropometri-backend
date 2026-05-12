<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Measurement\StoreMeasurementRequest;
use App\Http\Requests\Measurement\UpdateMeasurementRequest;
use App\Http\Resources\HistoryGroupedResource;
use App\Http\Resources\MeasurementResource;
use App\Http\Resources\MeasurementSummaryResource;
use App\Models\ActivityLog;
use App\Http\Resources\SubjectResource;
use App\Models\Measurement;
use App\Models\Subject;
use App\Services\MeasurementService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Measurement controller for measurement operations.
 * Per 07_api_specification.md §4
 */
class MeasurementController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected MeasurementService $measurementService
    ) {
    }

    /**
     * List measurements for a subject.
     * GET /subjects/{subject}/measurements
     */
    public function index(Request $request, Subject $subject): JsonResponse
    {
        $this->authorize('view', $subject);

        $measurements = $this->measurementService->getMeasurements(
            subject: $subject,
            filters: $request->only(['from_date', 'to_date']),
            perPage: $request->integer('per_page', 10)
        );

        return $this->successResponse(
            data: [
                'subject' => [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'category' => app(\App\Services\SubjectService::class)
                        ->determineCategory(
                            app(\App\Services\SubjectService::class)
                                ->calculateAgeInMonths($subject->date_of_birth)
                        ),
                ],
                'measurements' => MeasurementSummaryResource::collection($measurements->items())->resolve(),
                'pagination' => [
                    'current_page' => $measurements->currentPage(),
                    'last_page' => $measurements->lastPage(),
                    'per_page' => $measurements->perPage(),
                    'total' => $measurements->total(),
                ],
            ]
        );
    }

    /**
     * List all measurements for the authenticated user (History).
     * GET /measurements
     */
    public function history(Request $request): JsonResponse
    {
        $measurements = $this->measurementService->getAllMeasurements(
            filters: $request->only(['from_date', 'to_date', 'category', 'search', 'only_trashed']),
            perPage: $request->integer('per_page', 15)
        );

        return $this->successResponse(
            data: [
                'measurements' => MeasurementSummaryResource::collection($measurements->items())->resolve(),
                'pagination' => [
                    'current_page' => $measurements->currentPage(),
                    'last_page' => $measurements->lastPage(),
                    'per_page' => $measurements->perPage(),
                    'total' => $measurements->total(),
                ],
            ]
        );
    }

    /**
     * List grouped measurements by subject for unique patient list.
     * GET /measurements/grouped
     */
    public function groupedHistory(Request $request): JsonResponse
    {
        $subjects = $this->measurementService->getGroupedHistory(
            filters: $request->only(['search', 'only_trashed']),
            perPage: $request->integer('per_page', 10)
        );

        return $this->successResponse(
            data: [
                'subjects' => HistoryGroupedResource::collection($subjects->items())->resolve(),
                'pagination' => [
                    'current_page' => $subjects->currentPage(),
                    'last_page' => $subjects->lastPage(),
                    'per_page' => $subjects->perPage(),
                    'total' => $subjects->total(),
                ],
            ]
        );
    }

    /**
     * Create a new measurement with calculations.
     * POST /subjects/{subject}/measurements
     */
    public function store(StoreMeasurementRequest $request, Subject $subject): JsonResponse
    {
        $this->authorize('create', [Measurement::class, $subject]);

        $measurement = $this->measurementService->createMeasurement(
            subject: $subject,
            data: $request->validated()
        );
        ActivityLog::log('measurement_create', 'Measurement', $measurement->id, $measurement->toArray());

        // Send notification to all admins
        try {
            $weight = $measurement->weight ?? '-';
            $height = $measurement->height ?? '-';
            $admins = \App\Models\User::where('role', 'admin')->get();

            if ($admins->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\SystemNotification(
                    'Pemeriksaan Baru',
                    "{$subject->name} — BB: {$weight}kg, TB: {$height}cm (Petugas: {$request->user()->name})",
                    [
                        'measurement_id' => $measurement->id,
                        'subject_id' => $subject->id,
                        'subject_name' => $subject->name,
                    ]
                ));
            }
        } catch (\Exception $e) {
            \Log::error('Admin Notification error: ' . $e->getMessage());
        }

        return $this->successResponse(
            data: ['measurement' => new MeasurementResource($measurement->load('subject'))],
            message: 'Pengukuran berhasil disimpan',
            code: 201
        );
    }

    /**
     * Update a measurement and recalculate results.
     * PUT /measurements/{measurement}
     */
    public function update(UpdateMeasurementRequest $request, Measurement $measurement): JsonResponse
    {
        $this->authorize('update', $measurement);

        $oldValues = $measurement->toArray();
        $measurement = $this->measurementService->updateMeasurement(
            measurement: $measurement,
            data: $request->validated()
        );

        ActivityLog::log(
            'measurement_update',
            'Measurement',
            $measurement->id,
            $measurement->fresh()->toArray(),
            $oldValues
        );

        return $this->successResponse(
            data: ['measurement' => new MeasurementResource($measurement->load('subject'))],
            message: 'Pengukuran berhasil diperbarui'
        );
    }

    /**
     * Get measurement detail.
     * GET /measurements/{measurement}
     */
    public function show(Measurement $measurement): JsonResponse
    {
        $this->authorize('view', $measurement);

        return $this->successResponse(
            data: ['measurement' => new MeasurementResource($measurement->load('subject'))]
        );
    }

    /**
     * Delete a measurement.
     * DELETE /measurements/{measurement}
     */
    public function destroy(Measurement $measurement): JsonResponse
    {
        $this->authorize('delete', $measurement);

        $oldValues = $measurement->toArray();
        $measurement->delete();
        ActivityLog::log('measurement_delete', 'Measurement', $measurement->id, null, $oldValues);

        return $this->successResponse(
            message: 'Pengukuran berhasil dihapus'
        );
    }

    /**
     * Restore a deleted measurement.
     * POST /measurements/{id}/restore
     */
    public function restore(int $id): JsonResponse
    {
        $measurement = Measurement::onlyTrashed()->findOrFail($id);
        $this->authorize('delete', $measurement); // Reuse delete policy for restoration

        $measurement->restore();

        return $this->successResponse(
            message: 'Pengukuran berhasil dipulihkan'
        );
    }
}
