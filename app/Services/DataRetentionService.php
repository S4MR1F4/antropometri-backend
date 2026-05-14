<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Measurement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Symfony\Component\Process\Process;

class DataRetentionService
{
    public function pruneSoftDeleted(int $days = 60, bool $backup = true, bool $dryRun = false): array
    {
        $cutoff = now()->subDays($days);

        $subjectIds = Subject::withoutGlobalScope('user')
            ->onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->pluck('id');

        $measurementIds = Measurement::withoutGlobalScope('user')
            ->onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->when($subjectIds->isNotEmpty(), fn ($query) => $query->whereNotIn('subject_id', $subjectIds))
            ->pluck('id');

        $subjectMeasurementCount = $subjectIds->isEmpty()
            ? 0
            : Measurement::withoutGlobalScope('user')
                ->whereIn('subject_id', $subjectIds)
                ->withTrashed()
                ->count();

        $userIds = User::onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->pluck('id');

        $summary = [
            'cutoff' => $cutoff->toIso8601String(),
            'retention_days' => $days,
            'subjects' => $subjectIds->count(),
            'subject_measurements' => $subjectMeasurementCount,
            'measurements' => $measurementIds->count(),
            'users' => $userIds->count(),
            'reassigned_subjects' => 0,
            'reassigned_measurements' => 0,
            'backup_path' => null,
            'dry_run' => $dryRun,
        ];

        if ($this->nothingToPrune($summary)) {
            return $summary;
        }

        if ($dryRun) {
            return $summary;
        }

        if ($backup) {
            $summary['backup_path'] = $this->backupDatabase();
        }

        DB::transaction(function () use ($userIds, $measurementIds, $subjectIds, &$summary) {
            foreach ($userIds as $userId) {
                $user = User::withTrashed()->find($userId);
                if (! $user) {
                    continue;
                }

                $reassigned = $this->reassignUserDataToAdmin($user);
                $summary['reassigned_subjects'] += $reassigned['subjects'];
                $summary['reassigned_measurements'] += $reassigned['measurements'];
            }

            if ($measurementIds->isNotEmpty()) {
                Measurement::withoutGlobalScope('user')
                    ->withTrashed()
                    ->whereIn('id', $measurementIds)
                    ->forceDelete();
            }

            if ($subjectIds->isNotEmpty()) {
                Subject::withoutGlobalScope('user')
                    ->withTrashed()
                    ->whereIn('id', $subjectIds)
                    ->forceDelete();
            }

            if ($userIds->isNotEmpty()) {
                User::withTrashed()
                    ->whereIn('id', $userIds)
                    ->forceDelete();
            }
        });

        ActivityLog::log('data_retention_prune', 'System', null, $summary);

        return $summary;
    }

    public function reassignUserDataToAdmin(User $user, ?User $targetAdmin = null): array
    {
        $targetAdmin ??= $this->findTargetAdmin($user);

        if (! $targetAdmin) {
            throw new RuntimeException('Tidak ada admin aktif untuk menerima data user yang dihapus.');
        }

        $subjects = Subject::withoutGlobalScope('user')
            ->withTrashed()
            ->where('user_id', $user->id)
            ->update(['user_id' => $targetAdmin->id]);

        $measurements = Measurement::withoutGlobalScope('user')
            ->withTrashed()
            ->where('user_id', $user->id)
            ->update(['user_id' => $targetAdmin->id]);

        return [
            'target_admin_id' => $targetAdmin->id,
            'subjects' => $subjects,
            'measurements' => $measurements,
        ];
    }

    public function backupDatabase(): string
    {
        $script = base_path('scripts/backup_database.sh');
        if (! is_file($script)) {
            throw new RuntimeException("Backup script tidak ditemukan: {$script}");
        }

        $process = new Process(['bash', $script], base_path());
        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            $error = trim($process->getErrorOutput() ?: $process->getOutput());
            throw new RuntimeException('Backup database gagal: ' . $error);
        }

        return trim($process->getOutput());
    }

    private function findTargetAdmin(User $excluding): ?User
    {
        return User::query()
            ->where('role', 'admin')
            ->whereNull('deleted_at')
            ->where('id', '!=', $excluding->id)
            ->orderBy('id')
            ->first();
    }

    private function nothingToPrune(array $summary): bool
    {
        return $summary['subjects'] === 0
            && $summary['measurements'] === 0
            && $summary['users'] === 0;
    }
}
