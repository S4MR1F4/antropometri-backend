<?php

namespace App\Console\Commands;

use App\Services\DataRetentionService;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Backup database production using scripts/backup_database.sh';

    public function handle(DataRetentionService $dataRetentionService): int
    {
        try {
            $path = $dataRetentionService->backupDatabase();
            $this->info($path);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
