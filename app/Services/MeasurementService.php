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
     * Helper to get severity score
     */
    protected function getSeverityScore(?string $status): int
    {
        if (empty($status))
            return -1;
        $status = strtolower($status);

        if (str_contains($status, 'risiko kek') || str_contains($status, 'sangat kurus') || str_contains($status, 'gizi buruk') || str_contains($status, 'obesitas'))
            return 3;
        if (str_contains($status, 'kurus') || str_contains($status, 'gemuk') || str_contains($status, 'berisiko') || str_contains($status, 'gizi kurang') || str_contains($status, 'gizi lebih'))
            return 2;
        if (str_contains($status, 'normal') || str_contains($status, 'gizi baik'))
            return 1;

        return -1;
    }

    /**
     * Calculate health trend based on previous measurements.
     */
    public function calculateTrend(Subject $subject, ?array $currentResult, float $currentValue, string $category, string $metric = 'bmi'): ?array
    {
        $currentMeasurement = request()->route('measurement');
        $currentMeasurementId = $currentMeasurement instanceof Measurement
            ? $currentMeasurement->id
            : $currentMeasurement;

        $history = $subject->measurements()
            ->when($currentMeasurementId, fn($query) => $query->where('id', '!=', $currentMeasurementId))
            ->latest('measurement_date')
            ->latest('id')
            ->limit(5)
            ->get();

        if ($history->isEmpty()) {
            return null;
        }

        $lastMeasurement = $history->first();
        $prevValue = (float) ($metric === 'bmi' ? $lastMeasurement->bmi : $lastMeasurement->{$metric});

        if ($prevValue <= 0)
            return null;

        $diff = $currentValue - $prevValue;
        $status = 'stabil';
        $label = 'Stabil';

        // Extract statuses
        $currStatus = null;
        $prevStatus = null;

        if (isset($currentResult['is_pregnant']) && $currentResult['is_pregnant']) {
            $currStatus = $currentResult['status_lila'] ?? $currentResult['status_kek'] ?? null;
            $prevStatus = $lastMeasurement->status_lila ?? $lastMeasurement->status_kek;
        } elseif ($category === 'dewasa') {
            $currStatus = $currentResult['status_bmi'] ?? null;
            $prevStatus = $lastMeasurement->status_bmi;
        } else {
            // Remaja or Balita -> IMT or BB/TB
            $currStatus = $currentResult['status_imtu'] ?? $currentResult['status_bbtb'] ?? $currentResult['status_bmi'] ?? null;
            $prevStatus = $lastMeasurement->status_imtu ?? $lastMeasurement->status_bbtb ?? $lastMeasurement->status_bmi;
        }

        $currSeverity = $this->getSeverityScore($currStatus);
        $prevSeverity = $this->getSeverityScore($prevStatus);

        if ($currSeverity !== -1 && $prevSeverity !== -1) {
            if ($currSeverity < $prevSeverity) {
                $status = 'membaik';
                $label = 'Lebih Sehat';
            } elseif ($currSeverity > $prevSeverity) {
                $status = 'memburuk';
                $label = 'Menurun';
            } else {
                if ($diff > 0.05) {
                    $status = 'meningkat';
                    $label = 'Naik';
                } elseif ($diff < -0.05) {
                    $status = 'menurun';
                    $label = 'Turun';
                }
            }
        } else {
            if ($diff > 0.05) {
                $status = 'meningkat';
                $label = 'Naik';
            } elseif ($diff < -0.05) {
                $status = 'menurun';
                $label = 'Turun';
            }
        }

        // Velocity calculation (if more than 1 previous data point)
        $velocity = null;
        if ($history->count() >= 2) {
            $oldest = $history->last();
            $newest = $history->first();
            $months = $oldest->measurement_date->diffInMonths($newest->measurement_date) ?: 1;
            $oldestVal = (float) ($metric === 'bmi' ? $oldest->bmi : $oldest->{$metric});
            $newestVal = (float) ($metric === 'bmi' ? $newest->bmi : $newest->{$metric});
            $velocity = round(($newestVal - $oldestVal) / $months, 2);
        }

        $summary = $this->generateTrendSummary($status, $velocity, $metric, $diff, $prevValue, $currentValue);

        return [
            'previous_value' => round($prevValue, 2),
            'difference' => round($diff, 2),
            'status' => $status,
            'label' => $label,
            'velocity' => $velocity,
            'summary' => $summary,
            'date' => $lastMeasurement->measurement_date->toDateString(),
            'data_points_analyzed' => $history->count()
        ];
    }

    protected function generateTrendSummary(string $status, ?float $velocity, string $metric, float $diff, float $prev, float $curr): string
    {
        $metricName = $metric === 'bmi' ? 'IMT' : ($metric === 'weight' ? 'Berat Badan' : 'Tinggi Badan');
        $base = "";
        $diffStr = number_format(abs($diff), 1);
        $prevStr = number_format($prev, 1);
        $currStr = number_format($curr, 1);

        if ($status === 'membaik') {
            $base = "Kesehatan Menuju Lebih Baik. Perubahan $metricName $diffStr ($prevStr -> $currStr).";
        } elseif ($status === 'memburuk') {
            $base = "Kategori Kesehatan Menurun. Terdapat perubahan $metricName $diffStr ($prevStr -> $currStr).";
        } elseif ($status === 'stabil') {
            $base = "Kondisi terpantau stabil dengan $metricName $currStr.";
        } else {
            $direction = $status === 'meningkat' ? 'Naik' : 'Turun';
            $base = "$metricName $direction $diffStr dari pengukuran sebelumnya ($prevStr -> $currStr).";
        }

        if ($velocity !== null && $status !== 'stabil' && !in_array($status, ['membaik', 'memburuk'])) {
            $action = $velocity > 0 ? "peningkatan" : "penurunan";
            $absVelocity = abs($velocity);
            $base .= " Rata-rata $action sebesar $absVelocity per bulan.";
        }

        return $base;
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
        $isPregnant = isset($data['is_pregnant']) ? (bool) $data['is_pregnant'] : false;
        $category = $this->subjectService->determineCategory($ageInMonths, $isPregnant);

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

        // Calculate trend (Default to BMI for trend analysis)
        $trendMetric = in_array($category, ['dewasa', 'remaja']) ? 'bmi' : 'weight';
        $currentVal = $calculationResults[$trendMetric] ?? ($data['weight'] ?? 0);
        $trend = $this->calculateTrend($subject, $calculationResults, (float) $currentVal, $category, $trendMetric);

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

        // Handle Pregnancy Logic
        if ($isPregnant) {
            $measurementDate = \Carbon\Carbon::parse($data['measurement_date']);
            
            // 1. Persist pregnancy_start_date on subject if provided or not already set
            if (isset($data['pregnancy_start_date']) && $data['pregnancy_start_date']) {
                $subject->update(['pregnancy_start_date' => $data['pregnancy_start_date']]);
            } elseif (!$subject->pregnancy_start_date) {
                $subject->update(['pregnancy_start_date' => $measurementDate]);
            }

            // 2. Calculate Gestational Age (Weeks)
            $startDate = $subject->pregnancy_start_date ? \Carbon\Carbon::parse($subject->pregnancy_start_date) : $measurementDate;
            $gestationalWeeks = (int) $startDate->diffInWeeks($measurementDate);
            $measurementData['gestational_age_weeks'] = $gestationalWeeks;

            // 3. Determine Trimester
            $trimester = 1;
            if ($gestationalWeeks >= 27) {
                $trimester = 3;
            } elseif ($gestationalWeeks >= 14) {
                $trimester = 2;
            }
            $measurementData['trimester'] = $trimester;

            // 4. Calculate Weight Gain
            // Find the last measurement before pregnancy started
            $prePregnancyMeasurement = $subject->measurements()
                ->where('measurement_date', '<', $startDate)
                ->latest('measurement_date')
                ->first();

            if ($prePregnancyMeasurement instanceof \App\Models\Measurement && isset($prePregnancyMeasurement->weight)) {
                $measurementData['pregnancy_weight_gain'] = floatval($data['weight']) - floatval($prePregnancyMeasurement->weight);
            } else {
                // If no pre-pregnancy data, try to find the earliest pregnancy measurement
                $firstPregnancyMeasurement = $subject->measurements()
                    ->where('is_pregnant', true)
                    ->oldest('measurement_date')
                    ->first();
                
                if ($firstPregnancyMeasurement instanceof \App\Models\Measurement && isset($firstPregnancyMeasurement->weight)) {
                    $measurementData['pregnancy_weight_gain'] = floatval($data['weight']) - floatval($firstPregnancyMeasurement->weight);
                } else {
                    // Default to 0 if this is the first ever measurement
                    $measurementData['pregnancy_weight_gain'] = 0;
                }
            }
        }

        return Measurement::create($measurementData);
    }

    /**
     * Update an existing measurement and recalculate all derived fields.
     */
    public function updateMeasurement(Measurement $measurement, array $data): Measurement
    {
        $subject = $measurement->subject;

        $ageInMonths = $this->subjectService->calculateAgeInMonths(
            $subject->date_of_birth,
            $data['measurement_date']
        );
        $ageInYears = $this->subjectService->calculateAgeInYears(
            $subject->date_of_birth,
            $data['measurement_date']
        );

        $isPregnant = isset($data['is_pregnant']) ? (bool) $data['is_pregnant'] : false;
        $category = $this->subjectService->determineCategory($ageInMonths, $isPregnant);

        $calculationResults = $this->calculationService->calculate(
            subject: $subject,
            measurementData: $data,
            ageInMonths: $ageInMonths,
            category: $category,
        );

        $recommendation = $this->calculationService->generateRecommendation(
            category: $category,
            results: $calculationResults,
            data: $data,
        );

        $trendMetric = in_array($category, ['dewasa', 'remaja']) ? 'bmi' : 'weight';
        $currentVal = $calculationResults[$trendMetric] ?? ($data['weight'] ?? 0);
        $trend = $this->calculateTrend($subject, $calculationResults, (float) $currentVal, $category, $trendMetric);

        $measurementData = array_merge($data, [
            'subject_id' => $subject->id,
            'user_id' => $measurement->user_id,
            'category' => $category,
            'age_in_months' => $ageInMonths,
            'age_in_years' => $ageInYears,
            'recommendation' => $recommendation,
            'reference_data' => $calculationResults['references'] ?? null,
            'trend_info' => $trend,
        ], $calculationResults);

        if ($isPregnant) {
            $measurementDate = \Carbon\Carbon::parse($data['measurement_date']);
            if (!empty($data['pregnancy_start_date'])) {
                $subject->update(['pregnancy_start_date' => $data['pregnancy_start_date']]);
            } elseif (!$subject->pregnancy_start_date) {
                $subject->update(['pregnancy_start_date' => $measurementDate]);
            }

            $startDate = $subject->pregnancy_start_date ? \Carbon\Carbon::parse($subject->pregnancy_start_date) : $measurementDate;
            $gestationalWeeks = (int) $startDate->diffInWeeks($measurementDate);
            $measurementData['gestational_age_weeks'] = $gestationalWeeks;
            $measurementData['trimester'] = $gestationalWeeks >= 27 ? 3 : ($gestationalWeeks >= 14 ? 2 : 1);
        } else {
            $measurementData['gestational_age_weeks'] = null;
            $measurementData['trimester'] = null;
            $measurementData['pregnancy_weight_gain'] = null;
        }

        $measurement->update($measurementData);

        return $measurement->fresh();
    }

    /**
     * Create a measurement from offline sync data.
     * Use precise timestamp + value matching for deduplication.
     */
    public function createFromSyncData(array $syncData): Measurement
    {
        // Precise Deduplication: Check for identical timestamp + values
        $existing = Measurement::where('subject_id', $syncData['subject_id'])
            ->where('measurement_date', $syncData['measurement_date'])
            ->where('weight', $syncData['weight'] ?? null)
            ->where('height', $syncData['height'] ?? null)
            ->where('head_circumference', $syncData['head_circumference'] ?? null)
            ->first();

        if ($existing) {
            return $existing;
        }

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
        $isPregnant = isset($syncData['is_pregnant']) ? (bool) $syncData['is_pregnant'] : false;
        $category = $syncData['category'] ?? $this->subjectService->determineCategory($ageInMonths, $isPregnant);

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
        $trendMetric = in_array($category, ['dewasa', 'remaja']) ? 'bmi' : 'weight';
        $currentVal = $calculationResults[$trendMetric] ?? ($syncData['weight'] ?? 0);
        $trend = $this->calculateTrend($subject, $calculationResults, (float) $currentVal, $category, $trendMetric);

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
        $query = Measurement::with(['subject' => function($q) {
            $q->withTrashed();
        }, 'user']);

        if (!empty($filters['only_trashed'])) {
            $query->onlyTrashed();
        }

        // We use whereHas with withTrashed to ensure we see measurements 
        // even if their parent patient is deleted.
        $query->whereHas('subject', function($q) {
            $q->withTrashed();
        });

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

        if (!empty($filters['only_trashed'])) {
            $query->onlyTrashed();
        }

        // Search by subject name
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        // Filter by subjects that have measurements by this user
        $query->whereHas('measurements', function ($q) {
            if (!auth()->user()->isAdmin()) {
                $q->where('user_id', auth()->id());
            }
        });

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
