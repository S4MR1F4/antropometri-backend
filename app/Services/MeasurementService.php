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
     * Calculate health trend based on previous measurements.
     */
    public function calculateTrend(Subject $subject, float $currentBmi, string $category): ?array
    {
        $lastMeasurement = $subject->measurements()
            ->where('id', '!=', request()->route('measurement')) // Exclude current if updating
            ->latest('measurement_date')
            ->latest('id')
            ->first();

        if (!$lastMeasurement) {
            return null;
        }

        $prevBmi = (float) $lastMeasurement->bmi;
        if ($prevBmi <= 0)
            return null;

        $diff = $currentBmi - $prevBmi;
        $percent = ($diff / $prevBmi) * 100;

        $status = 'stabil';
        if ($diff > 0.1)
            $status = 'meningkat';
        if ($diff < -0.1)
            $status = 'menurun';

        return [
            'previous_bmi' => round($prevBmi, 2),
            'difference' => round($diff, 2),
            'percentage' => round($percent, 1),
            'status' => $status,
            'label' => ucfirst($status),
            'date' => $lastMeasurement->measurement_date->toDateString(),
        ];
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
        $ageInYears = $this->subjectService->calculateAgeInYears(
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
            data: $data,
        );

        // Calculate trend for adults/adolescents using BMI
        $trend = null;
        if (in_array($category, ['dewasa', 'remaja']) && isset($calculationResults['bmi'])) {
            $trend = $this->calculateTrend($subject, $calculationResults['bmi'], $category);
        }

        // Prepare measurement data
        $measurementData = array_merge($data, [
            'subject_id' => $subject->id,
            'user_id' => auth()->id(),
            'category' => $category,
            'age_in_months' => $ageInMonths,
            'age_in_years' => $ageInYears,
            'recommendation' => $recommendation,
            'reference_data' => $calculationResults['references'] ?? null,
            'trend_info' => $trend,
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
        $ageInYears = $this->subjectService->calculateAgeInYears(
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
            data: $syncData,
        );

        // Calculate trend
        $trend = null;
        if (in_array($category, ['dewasa', 'remaja']) && isset($calculationResults['bmi'])) {
            $trend = $this->calculateTrend($subject, $calculationResults['bmi'], $category);
        }

        // Prepare measurement data
        $measurementData = array_merge($syncData, [
            'subject_id' => $subject->id,
            'user_id' => auth()->id(),
            'category' => $category,
            'age_in_months' => $ageInMonths,
            'age_in_years' => $ageInYears,
            'recommendation' => $recommendation,
            'reference_data' => $calculationResults['references'] ?? null,
            'trend_info' => $trend,
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
        $query = Measurement::with(['subject', 'user']);

        // Only filter by user_id if NOT an admin
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $query->latest('measurement_date')->latest('id');

        if (!empty($filters['from_date'])) {
            $query->where('measurement_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->where('measurement_date', '<=', $filters['to_date']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                // Search by patient name
                $q->whereHas('subject', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })
                    // Or search by staff name
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get unique subjects with their latest measurement and measurement count.
     * Used for grouped history view in mobile app.
     */
    public function getGroupedHistory(array $filters = [], int $perPage = 15)
    {
        $query = Subject::query();

        // Search by subject name
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        // Filter by subjects that have measurements by this user (if not admin)
        if (!auth()->user()->isAdmin()) {
            $query->whereHas('measurements', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        // We allow subjects without measurements to show up in the patient list/history
        // for better visibility of newly added patients.
        // $query->has('measurements');

        // Load latest measurement and count
        $query->with([
            'latestMeasurement',
            'measurements' => function ($q) {
                if (!auth()->user()->isAdmin()) {
                    $q->where('user_id', auth()->id());
                }
            }
        ])->withCount([
                    'measurements' => function ($q) {
                        if (!auth()->user()->isAdmin()) {
                            $q->where('user_id', auth()->id());
                        }
                    }
                ]);

        // Order by latest measurement date using subquery for sorting
        $query->addSelect([
            'latest_measured_at' => Measurement::select('measurement_date')
                ->whereColumn('subject_id', 'subjects.id')
                ->latest('measurement_date')
                ->latest('id')
                ->limit(1)
        ])
            ->orderByDesc('latest_measured_at')
            ->orderByDesc('id');

        return $query->paginate($perPage);
    }
}
