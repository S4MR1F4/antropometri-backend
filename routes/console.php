<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('db:backup')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/database-backup.log'));

Schedule::command('data:prune-soft-deleted')
    ->dailyAt('02:15')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/data-retention.log'));
