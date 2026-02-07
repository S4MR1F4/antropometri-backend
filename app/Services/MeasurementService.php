<?php

namespace App\Services;

use App\Actions\Measurement\CalculateBalitaAction;
use App\Actions\Measurement\CalculateDewasaAction;
use App\Actions\Measurement\CalculateRemajaAction;
use App\Models\Measurement;
use App\Models\Subject;

/**
 * Measurement service layer.
 * Per 11_backend_architecture_laravel.md §5
 */
class MeasurementService
{
    public function __construct(
        protected SubjectService $subjectService,
        protected CalculationService $calculationService,
    ) {
    }

    /**
     * Create a measurement with calculations.
     */
    public function createMeasurement(Subject $subject, array $data): Measurement
    {
        // Calculate age at measurement
        $ageInMonths = $this->subjectService->calculateAgeInMonths(
            $subject->date_of_birth,
            $data['measurement_date']
        );

        // Determine category
        $category = $this->subjectService->determineCategory($ageInMonths);

        // Calculate results based on category
        $calculationResults = $this->calculationService->calculate(
            subject: $subject,
            measurementData: $data,
            ageInMonths: $ageInMonths,
            category: $category,
        );

        // Generate recommendation
        $recommendation = $this->calculationService->generateRecommendation(
            category: $category,
            results: $calculationResults,
        );

        // Prepare measurement data
        $measurementData = array_merge($data, [
            'subject_id' => $subject->id,
            'user_id' => auth()->id(),
            'category' => $category,
            'age_in_months' => $ageInMonths,
            'recommendation' => $recommendation,
        ], $calculationResults);

        return Measurement::create($measurementData);
    }

    /**
     * Create a measurement from offline sync data.
     * This method is used by SyncService for batch processing.
     *
     * @param array $syncData Data from offline sync with pre-calculated results
     * @return Measurement
     */
    public function createFromSyncData(array $syncData): Measurement
    {
        $subject = Subject::findOrFail($syncData['subject_id']);

        // Calculate age at measurement
        $ageInMonths = $this->subjectService->calculateAgeInMonths(
            $subject->date_of_birth,
            $syncData['measurement_date']
        );

        // Determine category
        $category = $syncData['category'] ?? $this->subjectService->determineCategory($ageInMonths);

        // If sync data already has calculated results, use them
        // Otherwise, calculate fresh
        if ($this->hasCalculatedResults($syncData, $category)) {
            $calculationResults = $this->extractCalculatedResults($syncData, $category);
        } else {
            $calculationResults = $this->calculationService->calculate(
                subject: $subject,
                measurementData: $syncData,
                ageInMonths: $ageInMonths,
                category: $category,
            );
        }

        // Generate recommendation if not provided
        $recommendation = $syncData['recommendation'] ?? $this->calculationService->generateRecommendation(
            category: $category,
            results: $calculationResults,
        );

        // Prepare measurement data
        $measurementData = array_merge($syncData, [
            'subject_id' => $subject->id,
            'user_id' => auth()->id(),
            'category' => $category,
            'age_in_months' => $ageInMonths,
            'recommendation' => $recommendation,
        ], $calculationResults);

        // Remove sync-specific fields
        unset($measurementData['local_id'], $measurementData['created_at_local'], $measurementData['hash']);

        return Measurement::create($measurementData);
    }

    /**
     * Check if sync data has pre-calculated results.
     */
    protected function hasCalculatedResults(array $data, string $category): bool
    {
        return match ($category) {
            'balita' => isset($data['zscore_bbu'], $data['status_bbu']),
            'remaja' => isset($data['zscore_imtu'], $data['status_imtu']),
            'dewasa' => isset($data['bmi'], $data['status_bmi']),
            default => false,
        };
    }

    /**
     * Extract calculated results from sync data.
     */
    protected function extractCalculatedResults(array $data, string $category): array
    {
        return match ($category) {
            'balita' => [
                'zscore_bbu' => $data['zscore_bbu'] ?? null,
                'zscore_tbu' => $data['zscore_tbu'] ?? null,
                'zscore_bbtb' => $data['zscore_bbtb'] ?? null,
                'status_bbu' => $data['status_bbu'] ?? null,
                'status_tbu' => $data['status_tbu'] ?? null,
                'status_bbtb' => $data['status_bbtb'] ?? null,
            ],
            'remaja' => [
                'bmi' => $data['bmi'] ?? null,
                'zscore_imtu' => $data['zscore_imtu'] ?? null,
                'status_imtu' => $data['status_imtu'] ?? null,
            ],
            'dewasa' => [
                'bmi' => $data['bmi'] ?? null,
                'status_bmi' => $data['status_bmi'] ?? null,
                'has_central_obesity' => $data['has_central_obesity'] ?? null,
            ],
            default => [],
        };
    }

    /**
     * Get measurements for a subject.
     */
    public function getMeasurements(Subject $subject, array $filters = [], int $perPage = 10)
    {
        $query = $subject->measurements()->latest('measurement_date');

        if (!empty($filters['from_date'])) {
            $query->where('measurement_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->where('measurement_date', '<=', $filters['to_date']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all measurements for the authenticated user.
     */
    public function getAllMeasurements(array $filters = [], int $perPage = 15)
    {
        $query = Measurement::where('user_id', auth()->id())
            ->with('subject')
            ->latest('measurement_date')
            ->latest('id');

        if (!empty($filters['from_date'])) {
            $query->where('measurement_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->where('measurement_date', '<=', $filters['to_date']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->paginate($perPage);
    }
}
