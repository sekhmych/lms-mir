<?php

namespace App\Console\Commands;

use App\Services\StepikSyncService;
use Illuminate\Console\Command;

class SyncStepikCourses extends Command
{
    protected $signature = 'stepik:sync';

    protected $description = 'Synchronize public Stepik courses into the local database';

    public function handle(StepikSyncService $stepikSyncService): int
    {
        $count = $stepikSyncService->sync();

        $this->info("Synchronized {$count} Stepik courses.");

        return self::SUCCESS;
    }
}