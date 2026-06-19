<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\PengaturanAbsensi;
use App\Services\AlpaService;
use Carbon\Carbon;

class DendaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        Carbon::setLocale('id');

        $bulanSelected = $request->input('bulan', Carbon::now()->month);
        $tahunSelected = $request->input('tahun', Carbon::now()->year);

        // Backfill past working days in selected month (up to yesterday, never today)
        $startOfMonth = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->startOfMonth()->format('Y-m-d');
        $endOfRange = Carbon::now()->subDay()->format('Y-m-d');
        if ($startOfMonth < $endOfRange) {
            AlpaService::backfillAlpaRecords($startOfMonth, $endOfRange);
        }

        $pengaturan = PengaturanAbsensi::first();
        $nominalDendaFlat = $pengaturan ? $pengaturan->denda_terlambat : 0;

        // Count totals from ALL records (not just current page)
        $baseQuery = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanSelected)
            ->whereYear('tanggal', $tahunSelected);

        $totalHariTelat = (clone $baseQuery)->where('status', 'Terlambat')->count();
        $totalHariAlpa = (clone $baseQuery)->where('status', 'Alpa')->count();
        $totalHariDenda = $totalHariTelat + $totalHariAlpa;
        $totalDenda = $totalHariDenda * $nominalDendaFlat;

        // Get paginated Terlambat and Alpa records
        $riwayatDenda = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanSelected)
            ->whereYear('tanggal', $tahunSelected)
            ->whereIn('status', ['Terlambat', 'Alpa'])
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $namaBulanTahun = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->translatedFormat('F Y');

        return view('guru.denda.index', compact(
            'riwayatDenda', 
            'totalHariTelat',
            'totalHariAlpa',
            'totalHariDenda', 
            'totalDenda', 
            'nominalDendaFlat',
            'bulanSelected',
            'tahunSelected',
            'namaBulanTahun'
        ));
    }
}