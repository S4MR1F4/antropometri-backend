<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Measurement\StoreMeasurementRequest;
use App\Http\Resources\MeasurementResource;
use App\Http\Resources\MeasurementSummaryResource;
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
                'measurements' => MeasurementSummaryResource::collection($measurements->items()),
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
            filters: $request->only(['from_date', 'to_date', 'category', 'search']),
            perPage: $request->integer('per_page', 15)
        );

        return $this->successResponse(
            data: [
                'measurements' => MeasurementSummaryResource::collection($measurements->items()),
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

        return $this->successResponse(
            data: ['measurement' => new MeasurementResource($measurement->load('subject'))],
            message: 'Pengukuran berhasil disimpan',
            code: 201
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

        $measurement->delete();

        return $this->successResponse(
            message: 'Pengukuran berhasil dihapus'
        );
    }
}
