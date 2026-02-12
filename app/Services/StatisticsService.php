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

        $totalMeasurements = (clone $query)->count();
        $measurementsToday = (clone $query)->whereDate('created_at', today())->count();

        // Trend Calculation (This Month vs Last Month)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        $thisMonthCount = (clone $query)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $lastMonthCount = (clone $query)->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

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
        $stats = [
            'normal' => 0,
            'stunting' => 0,
            'wasting' => 0,
            'obesity' => 0,
        ];

        // Process latest status for each subject in the filtered results
        $results = (clone $query)
            ->with('subject') // Assuming relationship exists
            ->orderBy('measurement_date', 'desc')
            ->get();

        // Group by subject to get latest status
        $uniqueSubjects = [];
        foreach ($results as $m) {
            if (!isset($uniqueSubjects[$m->subject_id])) {
                $uniqueSubjects[$m->subject_id] = $m;
            }
        }

        foreach ($uniqueSubjects as $m) {
            $isStunting = false;
            $isWasting = false;
            $isObesity = false;

            // Stunting logic (TB/U)
            if ($m->status_tbu) {
                $s = strtolower($m->status_tbu);
                if (str_contains($s, 'pendek'))
                    $isStunting = true;
            }

            // Wasting logic (BB/TB)
            if ($m->status_bbtb) {
                $s = strtolower($m->status_bbtb);
                if (str_contains($s, 'kurang') || str_contains($s, 'buruk') || str_contains($s, 'wasting') || str_contains($s, 'kurus')) {
                    $isWasting = true;
                }
                if (str_contains($s, 'lebih') || str_contains($s, 'gemuk') || str_contains($s, 'obesitas')) {
                    $isObesity = true;
                }
            }

            // Remaja/Dewasa BMI logic
            if ($m->status_imtu) {
                $s = strtolower($m->status_imtu);
                if (str_contains($s, 'kurus') || str_contains($s, 'kurang'))
                    $isWasting = true;
                if (str_contains($s, 'lebih') || str_contains($s, 'gemuk') || str_contains($s, 'obese'))
                    $isObesity = true;
            }

            if ($m->status_bmi) {
                $s = strtolower($m->status_bmi);
                if (str_contains($s, 'kurus') || str_contains($s, 'kurang'))
                    $isWasting = true;
                if (str_contains($s, 'lebih') || str_contains($s, 'gemuk') || str_contains($s, 'obesitas'))
                    $isObesity = true;
            }

            if ($isStunting)
                $stats['stunting']++;
            if ($isWasting)
                $stats['wasting']++;
            if ($isObesity)
                $stats['obesity']++;

            if (!$isStunting && !$isWasting && !$isObesity) {
                $stats['normal']++;
            }
        }

        return [
            // Mobile aligned keys
            'normal_count' => $stats['normal'],
            'stunting_count' => $stats['stunting'],
            'wasting_count' => $stats['wasting'],
            'obesity_count' => $stats['obesity'],

            // Backward compatibility for reports
            'gizi_baik' => $stats['normal'],
            'gizi_kurang' => $stats['stunting'], // Aligned logic: Stunting = Pendek/Kurang
            'gizi_buruk' => $stats['wasting'],  // Aligned logic: Wasting = Buruk/Kurus
            'gizi_lebih' => $stats['obesity'],
            'obesitas' => $stats['obesity'],
        ];
    }
}
