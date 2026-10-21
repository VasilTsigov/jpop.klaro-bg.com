<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Shared hosting: cron runs `php artisan schedule:run` every minute; this drains the mail queue.
// Activity journal retention (docs/03-plan.md §9)
Schedule::command('jpop:logs:prune')->dailyAt('03:30');
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')->everyMinute()->withoutOverlapping();
