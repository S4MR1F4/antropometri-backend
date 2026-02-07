<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Measurement;
use App\Models\Subject;
use App\Models\SyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * SyncService - Handles offline sync with conflict resolution
 * Per implementation_plan.md Phase 4
 */
class SyncService
{
    public function __construct(
        protected MeasurementService $measurementService,
        protected NotificationService $notificationService
    ) {
    }

    /**
     * Process a batch of offline measurements.
     *
     * @param array $records Array of measurement data with local_id, created_at_local, hash
     * @return array Sync results with synced, skipped, and conflicts
     */
    public function processBatch(array $records): array
    {
        $batchId = 'sync_' . Str::random(12);
        $userId = auth()->id();

        // Create sync log
        $syncLog = SyncLog::create([
            'user_id' => $userId,
            'batch_id' => $batchId,
            'total_records' => count($records),
            'status' => 'processing',
        ]);

        $synced = 0;
        $skipped = 0;
        $conflicts = [];
        $savedMeasurements = [];

        DB::beginTransaction();

        try {
            foreach ($records as $record) {
                $result = $this->processRecord($record);

                switch ($result['status']) {
                    case 'synced':
                        $synced++;
                        if (isset($result['measurement'])) {
                            $savedMeasurements[] = $result['measurement'];
                        }
                        break;
                    case 'skipped':
                        $skipped++;
                        break;
                    case 'conflict':
                        $conflicts[] = [
                            'local_id' => $record['local_id'] ?? null,
                            'reason' => $result['reason'],
                        ];
                        break;
                }
            }

            DB::commit();

            // Log sync event
            ActivityLog::log(
                'sync_batch',
                'SyncLog',
                $syncLog->id,
                [
                    'batch_id' => $batchId,
                    'total' => count($records),
                    'synced' => $synced,
                    'skipped' => $skipped,
                    'conflicts' => count($conflicts),
                ]
            );

            // Send batch notification summary
            if (count($savedMeasurements) > 0) {
                $this->notificationService->summarizeBatch($savedMeasurements);
            }

            $syncLog->complete($synced, $skipped, $conflicts);

        } catch (\Throwable $e) {
            DB::rollBack();
            $syncLog->fail($e->getMessage());

            ActivityLog::log('sync_failed', 'SyncLog', $syncLog->id, ['error' => $e->getMessage()]);

            throw $e;
        }

        return [
            'batch_id' => $batchId,
            'total' => count($records),
            'synced' => $synced,
            'skipped' => $skipped,
            'conflicts' => $conflicts,
        ];
    }

    /**
     * Process a single record with conflict resolution.
     */
    protected function processRecord(array $record): array
    {
        // Validate required fields
        if (!isset($record['subject_id'], $record['hash'], $record['measurement_date'])) {
            return [
                'status' => 'conflict',
                'reason' => 'Data tidak lengkap',
            ];
        }

        $hash = $this->normalizeHash($record['hash']);
        $createdAtLocal = $record['created_at_local'] ?? now();

        // Find existing measurement by hash (subject + date combination)
        $existing = Measurement::where('subject_id', $record['subject_id'])
            ->whereDate('measurement_date', $record['measurement_date'])
            ->first();

        if (!$existing) {
            // No duplicate - create new measurement
            $measurement = $this->measurementService->createFromSyncData($record);
            return ['status' => 'synced', 'measurement' => $measurement];
        }

        // Duplicate found - apply conflict resolution
        return $this->resolveConflict($existing, $record, $createdAtLocal);
    }

    /**
     * Resolve conflict between existing and incoming measurement.
     *
     * Rules:
     * - Same result + duplicate → keep earliest timestamp
     * - Different result + duplicate → reject with reason
     */
    protected function resolveConflict(Measurement $existing, array $incoming, string $createdAtLocal): array
    {
        $existingResult = $this->extractResult($existing);
        $incomingResult = $this->extractResultFromData($incoming);

        // Check if results are the same
        if ($existingResult === $incomingResult) {
            // Same result - keep the one with earliest timestamp
            if ($createdAtLocal < $existing->created_at) {
                // Incoming is older - update existing with older timestamp
                $existing->update(['created_at' => $createdAtLocal]);

                ActivityLog::log(
                    'sync_update',
                    'Measurement',
                    $existing->id,
                    ['reason' => 'Keeping earlier timestamp from sync']
                );

                return ['status' => 'synced', 'measurement' => $existing];
            }

            // Existing is older - skip incoming
            return ['status' => 'skipped'];
        }

        // Different results - conflict
        ActivityLog::log(
            'sync_conflict',
            'Measurement',
            $existing->id,
            [
                'existing_result' => $existingResult,
                'incoming_result' => $incomingResult,
                'local_id' => $incoming['local_id'] ?? null,
            ]
        );

        return [
            'status' => 'conflict',
            'reason' => 'Hasil berbeda dengan data server',
        ];
    }

    /**
     * Normalize hash for comparison.
     * Format: UPPERCASE(name) + DOB
     */
    protected function normalizeHash(string $hash): string
    {
        return strtoupper(trim($hash));
    }

    /**
     * Extract result summary from a Measurement model.
     */
    protected function extractResult(Measurement $measurement): string
    {
        return match ($measurement->category) {
            'balita' => $measurement->status_bbtb ?? '',
            'remaja' => $measurement->status_imtu ?? '',
            'dewasa' => $measurement->status_bmi ?? '',
            default => '',
        };
    }

    /**
     * Extract result summary from sync data array.
     */
    protected function extractResultFromData(array $data): string
    {
        return match ($data['category'] ?? '') {
            'balita' => $data['status_bbtb'] ?? '',
            'remaja' => $data['status_imtu'] ?? '',
            'dewasa' => $data['status_bmi'] ?? '',
            default => '',
        };
    }
}
