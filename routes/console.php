<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Models\HealthCheckResultHistoryItem;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('model:prune', ['--model' => [HealthCheckResultHistoryItem::class]])->daily();
Schedule::command('queue:prune-failed', ['--hours' => 168])->daily();
Schedule::command('sanctum:prune-expired', ['--hours' => 24])->daily();
Schedule::command('activitylog:clean')->daily();
Schedule::command('health:check')->everyMinute()->onOneServer();
Schedule::command('health:schedule-check-heartbeat')->everyMinute();
Schedule::command('health:queue-check-heartbeat')->everyMinute()->onOneServer();
