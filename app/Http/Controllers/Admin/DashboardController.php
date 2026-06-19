<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\IpLokal;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today();

        // 1. Hitung Total Guru
        $total_guru = User::where('role', 'guru')->count();

        // 2. Hitung Status Kehadiran Hari Ini
        // Sesuaikan nama kolom 'tanggal' dan 'status' dengan tabel database Anda
        $hadir = Absensi::whereDate('created_at', $hariIni)
                        ->where('status', 'Hadir')
                        ->count();

        $terlambat = Absensi::whereDate('created_at', $hariIni)
                            ->where('status', 'Terlambat')
                            ->count();

        // Alpa didapat dari Total Guru dikurangi yang sudah absen hari ini
        $total_sudah_absen = Absensi::whereDate('created_at', $hariIni)->distinct('user_id')->count('user_id');
        $alpa = $total_guru - $total_sudah_absen;
        // Pastikan tidak minus jika ada data anomali
        $alpa = $alpa < 0 ? 0 : $alpa; 

        $stats = [
            'total_guru' => $total_guru,
            'hadir'      => $hadir,
            'terlambat'  => $terlambat,
            'alpa'       => $alpa,
        ];

        // 3. Ambil 5 Aktivitas Absensi Terbaru Hari Ini
        $recent_absensi = Absensi::with('user') // Relasi ke model User
                            ->whereDate('created_at', $hariIni)
                            ->latest('jam_masuk') // Urutkan berdasarkan jam masuk terbaru
                            ->take(5)
                            ->get();

        // 4. Generate QR Code URL untuk absensi
        $qrAbsensiUrl = route('qr-absensi');

        return view('admin.dashboard.index', compact('stats', 'recent_absensi', 'qrAbsensiUrl'));
    }

    public function semuaAbsen(Request $request)
    {
        $query = Absensi::with('user')->orderBy('jam_masuk', 'desc');

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        } else {
            $query->where('tanggal', Carbon::today()->format('Y-m-d'));
        }

        $absensi = $query->paginate(10);

        return response()->json($absensi);
    }
}