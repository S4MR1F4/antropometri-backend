<?php

namespace App\Services;

use App\Models\Measurement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Statistics service for admin dashboard.
 * Per 07_api_specification.md §6.1
 */
class StatisticsService
{
    /**
     * Get aggregated dashboard statistics.
     */
    public function getDashboardStats(array $filters = []): array
    {
        $query = Measurement::query();

        if (!empty($filters['from_date'])) {
            $query->where('measurement_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->where('measurement_date', '<=', $filters['to_date']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Exclude measurements from deleted subjects
        $query->whereHas('subject');

        $totalMeasurements = (clone $query)->count();
        $measurementsToday = (clone $query)->whereDate('measurement_date', today())->count();

        // Trend Calculation (This Month vs Last Month)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        $thisMonthCount = (clone $query)->whereBetween('measurement_date', [$startOfMonth, $endOfMonth])->count();
        $lastMonthCount = (clone $query)->whereBetween('measurement_date', [$startOfLastMonth, $endOfLastMonth])->count();

        $growth = 0;
        if ($lastMonthCount > 0) {
            $growth = (($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
        } else if ($thisMonthCount > 0) {
            $growth = 100; // 100% growth if started from 0
        }

        // Breakdowns
        $byCategory = (clone $query)
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        // Status Distribution (Simplified for all indicators)
        // Note: For real prevalence, we'd need to prioritize which indicator to show
        // but for now we aggregate the most relevant one per category
        $byStatus = $this->getStatusDistribution($query);

        return [
            'total_users' => User::count(),
            'total_subjects' => Subject::count(),
            'total_measurements' => $totalMeasurements,
            'measurements_today' => $measurementsToday,
            'measurements_this_month' => $thisMonthCount,
            'measurements_last_month' => $lastMonthCount,
            'growth_percentage' => round($growth, 1),
            'by_category' => $byCategory,
            'by_status' => $byStatus,
        ];
    }

    /**
     * Helper to get health status distribution across different categories.
     * Aligned with Mobile labels: Normal, Stunting, Wasting, Obesity.
     */
    protected function getStatusDistribution($query): array
    {
        $stuntingCondition = "LOWER(COALESCE(status_tbu, '')) LIKE '%pendek%'";
        $wastingCondition = implode(' OR ', [
            "LOWER(COALESCE(status_bbtb, '')) LIKE '%kurang%'",
            "LOWER(COALESCE(status_bbtb, '')) LIKE '%buruk%'",
            "LOWER(COALESCE(status_bbtb, '')) LIKE '%wasting%'",
            "LOWER(COALESCE(status_bbtb, '')) LIKE '%kurus%'",
            "LOWER(COALESCE(status_imtu, '')) LIKE '%kurus%'",
            "LOWER(COALESCE(status_imtu, '')) LIKE '%kurang%'",
            "LOWER(COALESCE(status_bmi, '')) LIKE '%kurus%'",
            "LOWER(COALESCE(status_bmi, '')) LIKE '%kurang%'",
        ]);
        $obesityCondition = implode(' OR ', [
            "LOWER(COALESCE(status_bbtb, '')) LIKE '%lebih%'",
            "LOWER(COALESCE(status_bbtb, '')) LIKE '%gemuk%'",
            "LOWER(COALESCE(status_bbtb, '')) LIKE '%obesitas%'",
            "LOWER(COALESCE(status_imtu, '')) LIKE '%lebih%'",
            "LOWER(COALESCE(status_imtu, '')) LIKE '%gemuk%'",
            "LOWER(COALESCE(status_imtu, '')) LIKE '%obese%'",
            "LOWER(COALESCE(status_bmi, '')) LIKE '%lebih%'",
            "LOWER(COALESCE(status_bmi, '')) LIKE '%gemuk%'",
            "LOWER(COALESCE(status_bmi, '')) LIKE '%obesitas%'",
        ]);

        // Keep this as SQL aggregation. Pulling all measurements into PHP makes
        // the dashboard slow and memory-heavy once the table grows.
        $stats = (clone $query)
            ->selectRaw("SUM(CASE WHEN {$stuntingCondition} THEN 1 ELSE 0 END) as stunting")
            ->selectRaw("SUM(CASE WHEN {$wastingCondition} THEN 1 ELSE 0 END) as wasting")
            ->selectRaw("SUM(CASE WHEN {$obesityCondition} THEN 1 ELSE 0 END) as obesity")
            ->selectRaw("SUM(CASE WHEN NOT ({$stuntingCondition}) AND NOT ({$wastingCondition}) AND NOT ({$obesityCondition}) THEN 1 ELSE 0 END) as normal")
            ->first();

        $normal = (int) ($stats->normal ?? 0);
        $stunting = (int) ($stats->stunting ?? 0);
        $wasting = (int) ($stats->wasting ?? 0);
        $obesity = (int) ($stats->obesity ?? 0);

        return [
            // Mobile aligned keys
            'normal_count' => $normal,
            'stunting_count' => $stunting,
            'wasting_count' => $wasting,
            'obesity_count' => $obesity,

            // Backward compatibility for reports
            'gizi_baik' => $normal,
            'gizi_kurang' => $stunting, // Aligned logic: Stunting = Pendek/Kurang
            'gizi_buruk' => $wasting,  // Aligned logic: Wasting = Buruk/Kurus
            'gizi_lebih' => $obesity,
            'obesitas' => $obesity,
        ];
    }
}
