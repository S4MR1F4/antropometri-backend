<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User resource for API responses.
 * Per 07_api_specification.md §2
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'stats' => $this->when($request->routeIs('auth.me'), function () use ($request) {
                $statisticsService = app(\App\Services\StatisticsService::class);

                $filters = ['user_id' => $this->id];
                if ($request->filled('from_date'))
                    $filters['from_date'] = $request->input('from_date');
                if ($request->filled('to_date'))
                    $filters['to_date'] = $request->input('to_date');

                // Get stats filtered by this user's measurements
                $distribution = $statisticsService->getDashboardStats($filters);

                return [
                    'total_subjects' => $this->subjects()->count(),
                    'total_measurements' => $distribution['total_measurements'] ?? 0,
                    'total_all_time' => $this->measurements()->count(),
                    'today_measurements' => $this->measurements()
                        ->whereDate('created_at', today())
                        ->count(),
                    'growth_percentage' => $distribution['growth_percentage'] ?? 0,
                    'distribution' => $distribution['by_status'],
                ];
            }),
        ];
    }
}
