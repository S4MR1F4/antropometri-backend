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

        $totalMeasurements = (clone $query)->count();
        $measurementsToday = (clone $query)->whereDate('created_at', today())->count();

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
            'by_category' => $byCategory,
            'by_status' => $byStatus,
        ];
    }

    /**
     * Helper to get health status distribution across different categories.
     */
    protected function getStatusDistribution($query): array
    {
        // Combined distribution for Gizi status
        // We look at several status columns and map them to standard labels
        $stats = [
            'gizi_buruk' => 0,
            'gizi_kurang' => 0,
            'gizi_baik' => 0,
            'gizi_lebih' => 0,
            'obesitas' => 0,
            'lainnya' => 0,
        ];

        // This is a complex aggregation, so we do it in high level for dashboard
        $results = (clone $query)->get();

        foreach ($results as $m) {
            $status = null;
            if ($m->category === 'balita') {
                $status = $m->status_bbtb; // Using BB/TB as primary indicator for balita
            } elseif ($m->category === 'remaja') {
                $status = $m->status_imtu;
            } elseif ($m->category === 'dewasa') {
                $status = $m->status_bmi;
            }

            if (!$status)
                continue;

            $normalized = $this->normalizeStatus($status);
            if (isset($stats[$normalized])) {
                $stats[$normalized]++;
            } else {
                $stats['lainnya']++;
            }
        }

        return $stats;
    }

    protected function normalizeStatus(string $status): string
    {
        $status = strtolower($status);
        if (str_contains($status, 'buruk') || str_contains($status, 'sangat pendek') || str_contains($status, 'berat'))
            return 'gizi_buruk';
        if (str_contains($status, 'kurang') || str_contains($status, 'pendek') || str_contains($status, 'ringan'))
            return 'gizi_kurang';
        if (str_contains($status, 'baik') || str_contains($status, 'normal'))
            return 'gizi_baik';
        if (str_contains($status, 'lebih') || str_contains($status, 'gemuk'))
            return 'gizi_lebih';
        if (str_contains($status, 'obese') || str_contains($status, 'obesitas'))
            return 'obesitas';
        return 'lainnya';
    }
}
