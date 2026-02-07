<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DuplicateSubjectException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Http\Resources\SubjectCollection;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Services\SubjectService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Subject controller for subject CRUD operations.
 * Per 07_api_specification.md §3
 */
class SubjectController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected SubjectService $subjectService
    ) {
    }

    /**
     * List subjects with pagination and filters.
     * GET /subjects
     */
    public function index(Request $request): JsonResponse
    {
        $subjects = $this->subjectService->getSubjects(
            filters: $request->only(['search', 'category', 'gender', 'sort_by', 'sort_dir']),
            perPage: $request->integer('per_page', 15)
        );

        return $this->successResponse(
            data: new SubjectCollection($subjects)
        );
    }

    /**
     * Create a new subject.
     * POST /subjects
     */
    public function store(StoreSubjectRequest $request): JsonResponse
    {
        try {
            $subject = $this->subjectService->createSubject($request->validated());

            return $this->successResponse(
                data: ['subject' => new SubjectResource($subject)],
                message: 'Subjek berhasil ditambahkan',
                code: 201
            );
        } catch (DuplicateSubjectException $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                code: 422,
                data: [
                    'existing_subject' => [
                        'id' => $e->existingSubject->id,
                        'name' => $e->existingSubject->name,
                        'date_of_birth' => $e->existingSubject->date_of_birth->toDateString(),
                        'created_at' => $e->existingSubject->created_at->toIso8601String(),
                    ],
                ]
            );
        }
    }

    /**
     * Get subject detail.
     * GET /subjects/{subject}
     */
    public function show(Subject $subject): JsonResponse
    {
        $this->authorize('view', $subject);

        $subject->load('latestMeasurement');
        $subject->loadCount('measurements');

        return $this->successResponse(
            data: ['subject' => new SubjectResource($subject)]
        );
    }

    /**
     * Update a subject.
     * PUT /subjects/{subject}
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): JsonResponse
    {
        $this->authorize('update', $subject);

        try {
            $subject = $this->subjectService->updateSubject($subject, $request->validated());

            return $this->successResponse(
                data: ['subject' => new SubjectResource($subject)],
                message: 'Subjek berhasil diperbarui'
            );
        } catch (DuplicateSubjectException $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                code: 422,
                data: [
                    'existing_subject' => [
                        'id' => $e->existingSubject->id,
                        'name' => $e->existingSubject->name,
                        'date_of_birth' => $e->existingSubject->date_of_birth->toDateString(),
                    ],
                ]
            );
        }
    }

    /**
     * Delete a subject (soft delete).
     * DELETE /subjects/{subject}
     */
    public function destroy(Subject $subject): JsonResponse
    {
        $this->authorize('delete', $subject);

        $subject->delete();

        return $this->successResponse(
            message: 'Subjek berhasil dihapus'
        );
    }
}
