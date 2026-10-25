<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Temporarily disabled to prevent automatic backup loops and excessive bandwidth consumption.
// Manual backups can still be triggered from the Admin Dashboard or via `php artisan db:backup`.
// Schedule::command('db:backup')->twiceDaily(0, 12);

