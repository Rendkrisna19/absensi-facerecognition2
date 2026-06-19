<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\User;
use App\Models\LiburSemester;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AlpaService
{
    /**
     * Check if a given date is a working day (not Sunday, not semester holiday, not national holiday).
     */
    public static function isWorkingDay(string $date): bool
    {
        $carbon = Carbon::parse($date);

        // Sunday is always off
        if ($carbon->isSunday()) {
            return false;
        }

        // Check semester holiday
        $liburSemester = LiburSemester::where('is_active', true)
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->first();

        if ($liburSemester) {
            return false;
        }

        // Check national holiday via API
        try {
            $response = Http::timeout(3)->get(
                'https://libur.deno.dev/api?year=' . $carbon->year . '&month=' . $carbon->month . '&day=' . $carbon->day
            );
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['is_holiday']) && $data['is_holiday']) {
                    return false;
                }
            }
        } catch (\Exception $e) {
            // If API fails, assume it's a working day
        }

        return true;
    }

    /**
     * Create Alpa records for all gurus who did not attend on a given date.
     * Only processes PAST dates — never today or future (school day may still be ongoing).
     * Returns the number of new Alpa records created.
     */
    public static function createAlpaRecords(?string $date = null): int
    {
        $date = $date ?? Carbon::now()->subDay()->format('Y-m-d');

        // NEVER create Alpa for today or future — teachers haven't finished scanning yet
        $today = Carbon::now()->format('Y-m-d');
        if ($date >= $today) {
            return 0;
        }

        if (!self::isWorkingDay($date)) {
            return 0;
        }

        $gurus = User::where('role', 'guru')->get();
        $created = 0;

        foreach ($gurus as $guru) {
            $exists = Absensi::where('user_id', $guru->id)
                ->where('tanggal', $date)
                ->first();

            if (!$exists) {
                Absensi::create([
                    'user_id'         => $guru->id,
                    'tanggal'         => $date,
                    'jam_masuk'       => null,
                    'jam_pulang'      => null,
                    'status'          => 'Alpa',
                    'menit_terlambat' => 0,
                ]);
                $created++;
            }
        }

        return $created;
    }

    /**
     * Backfill Alpa records for a date range.
     * Only processes dates that are working days and skips dates already fully recorded.
     */
    public static function backfillAlpaRecords(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $today = Carbon::now()->format('Y-m-d');
        $totalCreated = 0;

        // Don't backfill for today or future dates
        if ($start->format('Y-m-d') >= $today) {
            return 0;
        }

        // End date should not exceed yesterday
        if ($end->format('Y-m-d') >= $today) {
            $end = Carbon::parse($today)->subDay();
        }

        $current = $start->copy();
        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $totalCreated += self::createAlpaRecords($dateStr);
            $current->addDay();
        }

        return $totalCreated;
    }
}
