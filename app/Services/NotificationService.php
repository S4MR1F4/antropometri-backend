<?php

namespace App\Services;

use App\Models\Measurement;
use App\Models\ActivityLog;

/**
 * NotificationService - Handles notification triggers
 * Per implementation_plan.md Phase 4
 *
 * Note: Notifications are triggered ONLY on successful save, not on calculation-only.
 */
class NotificationService
{
    /**
     * Notify on single measurement save.
     * Called after successful measurement creation.
     */
    public function notifyOnSave(Measurement $measurement): void
    {
        // Log the notification trigger
        ActivityLog::log(
            'notification_triggered',
            'Measurement',
            $measurement->id,
            [
                'subject_id' => $measurement->subject_id,
                'category' => $measurement->category,
                'status' => $this->getStatusSummary($measurement),
            ]
        );

        // Here you would integrate with actual notification channels:
        // - Push notification
        // - SMS
        // - Email
        // For now, we just log the event
    }

    /**
     * Summarize batch sync and send notification.
     *
     * @param array<Measurement> $measurements
     */
    public function summarizeBatch(array $measurements): void
    {
        if (empty($measurements)) {
            return;
        }

        $summary = [
            'total' => count($measurements),
            'categories' => [],
            'statuses' => [],
        ];

        foreach ($measurements as $measurement) {
            $category = $measurement->category;
            $status = $this->getStatusSummary($measurement);

            $summary['categories'][$category] = ($summary['categories'][$category] ?? 0) + 1;
            $summary['statuses'][$status] = ($summary['statuses'][$status] ?? 0) + 1;
        }

        // Log batch notification
        ActivityLog::log(
            'notification_batch',
            null,
            null,
            $summary
        );

        // Integrate with actual notification service here
    }

    /**
     * Get status summary from measurement.
     */
    protected function getStatusSummary(Measurement $measurement): string
    {
        return match ($measurement->category) {
            'balita' => $measurement->status_bbtb ?? 'unknown',
            'remaja' => $measurement->status_imtu ?? 'unknown',
            'dewasa' => $measurement->status_bmi ?? 'unknown',
            default => 'unknown',
        };
    }
}
