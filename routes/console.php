<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan pembuatan record Alpa setiap hari setelah tengah malam (untuk hari sebelumnya)
// Ini memastikan semua guru sudah selesai scan di hari sebelumnya
Schedule::command('absensi:create-alpa')->dailyAt('00:30');
