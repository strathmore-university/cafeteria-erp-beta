<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('update:expired-purchase-orders')
    ->everySixHours();

Schedule::command('auto:review-cooking-shifts')
    ->dailyAt('00:00')
    ->onSuccess(function (): void {
        Artisan::call('auto:create-cooking-shifts');
        Artisan::call('auto:populate-cooking-shifts');
    });
