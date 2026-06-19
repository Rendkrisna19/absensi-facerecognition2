<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\PengaturanAbsensi;
use App\Models\LiburSemester; 
use App\Models\PengajuanIzin;
use App\Services\AlpaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        Carbon::setLocale('id');
        $hariIni = Carbon::now();
        $bulanIni = $hariIni->month;
        $tahunIni = $hariIni->year;
        $tanggalFormat = $hariIni->translatedFormat('l, d F Y');

        // Salam dinamis berdasarkan jam WIB (Asia/Jakarta)
        $jam = $hariIni->hour;
        if ($jam >= 4 && $jam <= 10) {
            $greeting = 'Selamat Pagi';
        } elseif ($jam >= 11 && $jam <= 14) {
            $greeting = 'Selamat Siang';
        } elseif ($jam >= 15 && $jam <= 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        // Backfill Alpa records for past working days this month (not today)
        $startOfMonth = Carbon::createFromDate($tahunIni, $bulanIni, 1)->format('Y-m-d');
        $yesterday = $hariIni->copy()->subDay()->format('Y-m-d');
        if ($startOfMonth <= $yesterday) {
            AlpaService::backfillAlpaRecords($startOfMonth, $yesterday);
        }
        
        // 1. AMBIL PENGATURAN ABSENSI (Untuk tahu jam pulang)
        $pengaturan = PengaturanAbsensi::first();
        $jamPulang = $pengaturan ? $pengaturan->jam_pulang : '14:00:00';
        $nominalDendaFlat = $pengaturan ? $pengaturan->denda_terlambat : 0;

        // 2. CEK STATUS ABSEN HARI INI
        $absenHariIni = Absensi::where('user_id', $user->id)
                               ->where('tanggal', $hariIni->format('Y-m-d'))
                               ->first();

        // 3. CEK PENGAJUAN IZIN HARI INI
        $izinHariIni = PengajuanIzin::where('user_id', $user->id)
                            ->whereDate('tanggal_mulai', '<=', $hariIni->format('Y-m-d'))
                            ->whereDate('tanggal_selesai', '>=', $hariIni->format('Y-m-d'))
                            ->whereIn('status', ['Pending', 'Disetujui'])
                            ->first();

        // 4. LOGIKA HARI LIBUR
        $isLibur = false;
        $keteranganLibur = '';

        $liburSemester = LiburSemester::where('is_active', true)
                            ->whereDate('tanggal_mulai', '<=', $hariIni->format('Y-m-d'))
                            ->whereDate('tanggal_selesai', '>=', $hariIni->format('Y-m-d'))
                            ->first();

        if ($liburSemester) {
            $isLibur = true;
            $keteranganLibur = 'Libur Semester: ' . $liburSemester->nama_semester;
        } elseif ($hariIni->isSunday()) {
            $isLibur = true;
            $keteranganLibur = 'Libur Akhir Pekan (Minggu)';
        } else {
            try {
                $response = Http::timeout(3)->get('https://libur.deno.dev/api?year=' . $hariIni->year . '&month=' . $hariIni->month . '&day=' . $hariIni->day);
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['is_holiday']) && $data['is_holiday']) {
                        $isLibur = true;
                        $keteranganLibur = !empty($data['holiday_list']) ? $data['holiday_list'][0] : 'Libur Nasional / Cuti Bersama';
                    }
                }
            } catch (\Exception $e) {}
        }

        // 5. STATISTIK BULAN INI
        $totalHadirBulanIni = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', '!=', 'Alpa')
            ->count();

        $totalTelatBulanIni = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'Terlambat')
            ->count();

        $totalAlpaBulanIni = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'Alpa')
            ->count();

        $totalDendaBulanIni = ($totalTelatBulanIni + $totalAlpaBulanIni) * $nominalDendaFlat;

        // 6. RIWAYAT PENGAJUAN IZIN
        $riwayatIzin = PengajuanIzin::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(3);

        return view('guru.dashboard.index', compact(
            'absenHariIni', 
            'izinHariIni',
            'tanggalFormat', 
            'isLibur', 
            'keteranganLibur',
            'totalHadirBulanIni',
            'totalDendaBulanIni',
            'riwayatIzin',
            'jamPulang',
            'greeting'
        ));
    }
}