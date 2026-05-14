<?php

namespace App\Console\Commands;

use App\Services\DataRetentionService;
use Illuminate\Console\Command;
use Throwable;

class PruneSoftDeletedData extends Command
{
    protected $signature = 'data:prune-soft-deleted
        {--days= : Jumlah hari retensi soft delete sebelum force delete}
        {--dry-run : Hitung data yang akan dihapus tanpa backup dan tanpa force delete}
        {--skip-backup : Lewati backup database sebelum force delete}';

    protected $description = 'Backup database lalu hapus permanen data soft-deleted yang melewati masa retensi.';

    public function handle(DataRetentionService $service): int
    {
        $days = (int) ($this->option('days') ?: env('DATA_RETENTION_SOFT_DELETE_DAYS', 60));
        $days = max(1, $days);
        $dryRun = (bool) $this->option('dry-run');
        $backup = ! $dryRun && ! (bool) $this->option('skip-backup');

        try {
            $summary = $service->pruneSoftDeleted(
                days: $days,
                backup: $backup,
                dryRun: $dryRun,
            );
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Data retention selesai.');
        $this->line(json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }
}
