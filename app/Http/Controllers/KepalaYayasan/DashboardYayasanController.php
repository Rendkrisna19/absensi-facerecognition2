<?php

namespace App\Http\Controllers\KepalaYayasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;
use App\Models\PengaturanAbsensi;
use App\Services\AlpaService;
use Carbon\Carbon;

class DashboardYayasanController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $hariIni = Carbon::now()->format('Y-m-d');

        // Backfill Alpa records for current month
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now()->subDay()->format('Y-m-d');
        if ($startDate < $endDate) {
            AlpaService::backfillAlpaRecords($startDate, $endDate);
        }
        
        $bulanSelected = $request->input('bulan', Carbon::now()->month);
        $tahunSelected = $request->input('tahun', Carbon::now()->year);
        $unitSelected = $request->input('unit_sekolah', 'all');

        // Query Builder for User (Guru)
        $userQuery = User::where('role', 'guru');
        if ($unitSelected !== 'all') {
            $userQuery->where('unit_sekolah', $unitSelected);
        }
        $totalGuru = $userQuery->count();

        // Helper function for Absensi query with unit filter
        $getAbsensiQuery = function() use ($unitSelected) {
            $query = Absensi::whereHas('user', function($q) {
                $q->where('role', 'guru');
            });
            if ($unitSelected !== 'all') {
                $query->whereHas('user', function($q) use ($unitSelected) {
                    $q->where('unit_sekolah', $unitSelected);
                });
            }
            return $query;
        };

        // 3. METRIK EKSEKUTIF HARI INI
        $absenHariIni = $getAbsensiQuery()->where('tanggal', $hariIni)->get();
        $hadirTepat = $absenHariIni->where('status', 'Hadir')->count();
        $terlambatHariIni = $absenHariIni->where('status', 'Terlambat')->count();
        
        $sudahAbsen = $hadirTepat + $terlambatHariIni;
        $alpaHariIni = $totalGuru > 0 ? max(0, $totalGuru - $sudahAbsen) : 0;

        // 4. METRIK DENDA BULAN INI
        $pengaturan = PengaturanAbsensi::first();
        $nominalDendaFlat = $pengaturan ? $pengaturan->denda_terlambat : 0;
        
        $totalTelatBulanIni = $getAbsensiQuery()
                                     ->whereMonth('tanggal', $bulanSelected)
                                     ->whereYear('tanggal', $tahunSelected)
                                     ->where('status', 'Terlambat')
                                     ->count();
        
        $totalAlpaBulanIni = $getAbsensiQuery()
                                     ->whereMonth('tanggal', $bulanSelected)
                                     ->whereYear('tanggal', $tahunSelected)
                                     ->where('status', 'Alpa')
                                     ->count();
                                              
        $totalDendaBulanIni = ($totalTelatBulanIni + $totalAlpaBulanIni) * $nominalDendaFlat;

        $rataKehadiran = $totalGuru > 0 ? round(($sudahAbsen / $totalGuru) * 100) : 0;

        $metrics = [
            'total_guru' => $totalGuru,
            'hadir_tepat' => $hadirTepat,
            'terlambat_hari_ini' => $terlambatHariIni,
            'alpa_hari_ini' => $alpaHariIni,
            'total_denda_bulan_ini' => $totalDendaBulanIni,
            'rata_kehadiran' => $rataKehadiran,
        ];

        // 5. DATA 5 ABSEN TERAKHIR
        $absenTerakhir = $getAbsensiQuery()
            ->with('user')
            ->where('tanggal', $hariIni)
            ->orderBy('jam_masuk', 'desc')
            ->take(5)
            ->get();

        // 6. GRAFIK TREN MINGGUAN
        $labelHari = [];
        $dataHadir = [];
        $dataTelat = [];
        $dataAlpa = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::now()->subDays($i);
            $labelHari[] = $tgl->translatedFormat('D, d M'); 

            $absenHarian = $getAbsensiQuery()->where('tanggal', $tgl->format('Y-m-d'))->get();
            $h = $absenHarian->where('status', 'Hadir')->count();
            $t = $absenHarian->where('status', 'Terlambat')->count();
            $a = $totalGuru > 0 ? max(0, $totalGuru - ($h + $t)) : 0;

            $dataHadir[] = $h;
            $dataTelat[] = $t;
            $dataAlpa[] = $a;
        }

        $chartData = [
            'labels' => $labelHari,
            'hadir' => $dataHadir,
            'terlambat' => $dataTelat,
            'alpa' => $dataAlpa
        ];

        $bulanSekarang = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->translatedFormat('F Y');

        return view('kepala-yayasan.dashboard', compact(
            'metrics', 
            'chartData', 
            'bulanSekarang', 
            'absenTerakhir', 
            'bulanSelected', 
            'tahunSelected',
            'unitSelected'
        ));
    }
}