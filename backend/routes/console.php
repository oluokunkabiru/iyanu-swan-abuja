<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('dues:send-yearly-reminders')->yearlyOn(1, 1, '06:00');
Schedule::command('birthdays:send-greetings')->dailyAt('07:00');
