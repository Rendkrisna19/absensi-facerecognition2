<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\IpLokal;
use App\Models\PengajuanIzin;      
use App\Models\PengaturanAbsensi;
use App\Models\LiburSemester; 
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class ScanAbsensiController extends Controller
{
    private function cekJaringanWifi($ipUser)
    {
        $allowedIps = IpLokal::where('is_active', true)->pluck('ip_address');
        foreach ($allowedIps as $allowedIp) {
            // 1. Cek Exact Match atau pola Wildcard bawaan (%)
            $pattern = str_replace('%', '*', $allowedIp);
            if (Str::is($pattern, $ipUser)) return true;

            // 2. Auto-Subnet IPv4 (Memeriksa 2 blok angka pertama)
            // Karena IP Publik ISP di Indonesia (seperti Telkomsel/Indihome) sangat dinamis (contoh: 114.10.84.193 bisa berubah jadi 114.10.85.10)
            $partsAllowed = explode('.', $allowedIp);
            $partsUser = explode('.', $ipUser);
            
            if (count($partsAllowed) === 4 && count($partsUser) === 4) {
                // Hanya periksa 2 blok pertama (misal: 114.10.x.x)
                if ($partsAllowed[0] === $partsUser[0] && 
                    $partsAllowed[1] === $partsUser[1]) {
                    return true;
                }
            }

            // 3. Auto-Subnet IPv6 (Mencocokkan 4 blok awal / Prefix ISP)
            $ipv6Allowed = explode(':', $allowedIp);
            $ipv6User = explode(':', $ipUser);
            if (count($ipv6Allowed) >= 4 && count($ipv6User) >= 4) {
                // Cek 4 blok pertama (Prefix IPv6 yang diberikan ISP ke Router Sekolah)
                // Ini mencegah HP dari rumah dengan provider yang sama (awalan 2001:) ikut lolos
                if ($ipv6Allowed[0] === $ipv6User[0] && 
                    $ipv6Allowed[1] === $ipv6User[1] && 
                    $ipv6Allowed[2] === $ipv6User[2] &&
                    $ipv6Allowed[3] === $ipv6User[3]) {
                    return true;
                }
            }
        }
        return false;
    }

    public function index()
    {
        $user = auth()->user();
        $wajahTerdaftar = !empty($user->face_descriptor);
        
        $ipUser = request()->ip();
        $ipValid = $this->cekJaringanWifi($ipUser);

        $pengaturan = PengaturanAbsensi::first();
        $jamSekarang = Carbon::now()->format('H:i:s');
        $hariIni = Carbon::now()->format('Y-m-d');
        
        $isWaktuAbsen = true;
        $pesanWaktu = '';

        // ---> VALIDASI JARINGAN & LIBUR & IZIN <---
        if (!$ipValid) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Anda tidak terhubung ke jaringan WiFi sekolah.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        $liburSemester = LiburSemester::where('is_active', true)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->first();
        if ($liburSemester) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Saat ini sedang masa ' . $liburSemester->nama_semester . '. Sistem absensi ditutup.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        } elseif (Carbon::now()->isSunday()) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Hari ini adalah hari Minggu (Libur Akhir Pekan). Sistem absensi ditutup.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        } else {
            try {
                $response = Http::timeout(3)->get('https://libur.deno.dev/api?year=' . Carbon::now()->year . '&month=' . Carbon::now()->month . '&day=' . Carbon::now()->day);
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['is_holiday']) && $data['is_holiday']) {
                        $isWaktuAbsen = false;
                        $ketLibur = !empty($data['holiday_list']) ? $data['holiday_list'][0] : 'Libur Nasional';
                        $pesanWaktu = 'Hari ini adalah ' . $ketLibur . '. Sistem absensi ditutup.';
                        return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
                    }
                }
            } catch (\Exception $e) {}
        }

        $izinHariIni = PengajuanIzin::where('user_id', $user->id)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->whereIn('status', ['Pending', 'Disetujui'])
                            ->first();
        if ($izinHariIni) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Anda memiliki pengajuan ' . $izinHariIni->jenis . ' hari ini. Anda tidak dapat melakukan absensi.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        if (!$pengaturan) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Pengaturan jadwal absensi belum dikonfigurasi.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        // ---> LOGIKA STATUS ABSENSI (MASUK / PULANG) <---
        $absenHariIni = Absensi::where('user_id', $user->id)->where('tanggal', $hariIni)->first();
        
        $jamBukaAbsen = $pengaturan->jam_buka_absen ?? '00:00:00'; 
        $jamPulang    = $pengaturan->jam_pulang ?? '14:00:00';

        if (!$absenHariIni) {
            // BELUM ABSEN MASUK
            if ($jamSekarang < $jamBukaAbsen) {
                $isWaktuAbsen = false;
                $pesanWaktu = 'Absensi MASUK belum dibuka. Silakan kembali pada pukul ' . Carbon::parse($jamBukaAbsen)->format('H:i') . ' WIB.';
            }
        } else {
            // SUDAH ABSEN MASUK
            if (empty($absenHariIni->jam_pulang)) {
                // BELUM ABSEN PULANG
                if ($jamSekarang < $jamPulang) {
                    $isWaktuAbsen = false;
                    $pesanWaktu = 'Anda sudah Absen Masuk. Absensi PULANG baru akan dibuka pukul ' . Carbon::parse($jamPulang)->format('H:i') . ' WIB.';
                }
            } else {
                // SUDAH ABSEN PULANG
                $isWaktuAbsen = false;
                $pesanWaktu = 'Anda telah menyelesaikan Absensi Masuk dan Pulang hari ini. Selamat beristirahat!';
            }
        }

        return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $hariIni = Carbon::now()->format('Y-m-d');
        $jamSekarang = Carbon::now()->format('H:i:s');
        
        $ipUser = request()->ip();
        if (!$this->cekJaringanWifi($ipUser)) {
            return response()->json(['success' => false, 'message' => 'Gagal! Anda tidak terhubung ke WiFi sekolah.']);
        }

        // Cek Libur
        $liburSemester = LiburSemester::where('is_active', true)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->first();
        if ($liburSemester || Carbon::now()->isSunday()) {
            return response()->json(['success' => false, 'message' => 'Sistem absensi ditutup karena hari libur.']);
        }

        try {
            $response = Http::timeout(3)->get('https://libur.deno.dev/api?year=' . Carbon::now()->year . '&month=' . Carbon::now()->month . '&day=' . Carbon::now()->day);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['is_holiday']) && $data['is_holiday']) {
                    return response()->json(['success' => false, 'message' => 'Sistem absensi ditutup karena hari libur nasional.']);
                }
            }
        } catch (\Exception $e) {}

        $pengaturan = PengaturanAbsensi::first();
        if (!$pengaturan) {
            return response()->json(['success' => false, 'message' => 'Gagal! Pengaturan jadwal absensi belum ada.']);
        }

        $jamBukaAbsen = $pengaturan->jam_buka_absen ?? '00:00:00'; 
        $batasMasuk   = $pengaturan->batas_jam_masuk ?? '07:15:00';
        $jamPulang    = $pengaturan->jam_pulang ?? '14:00:00';
        
        $absenHariIni = Absensi::where('user_id', $user->id)->where('tanggal', $hariIni)->first();

        // ========================================================
        // EKSEKUSI PENYIMPANAN DATA (MASUK ATAU PULANG)
        // ========================================================
        if (!$absenHariIni) {
            
            // 1. PROSES ABSEN MASUK
            if ($jamSekarang < $jamBukaAbsen) {
                return response()->json(['success' => false, 'message' => 'Absensi masuk belum dibuka.']);
            }

            $status = 'Hadir';
            $menitTerlambat = 0;

            if ($jamSekarang > $batasMasuk) {
                $status = 'Terlambat';
                $waktuBatas = Carbon::parse($batasMasuk);
                $waktuMasuk = Carbon::parse($jamSekarang);
                $menitTerlambat = $waktuBatas->diffInMinutes($waktuMasuk);
            }

            Absensi::create([
                'user_id' => $user->id,
                'tanggal' => $hariIni,
                'jam_masuk' => $jamSekarang,
                'status' => $status,
                'menit_terlambat' => $menitTerlambat
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absensi MASUK berhasil dicatat pada ' . Carbon::parse($jamSekarang)->format('H:i') . ' WIB.'
            ]);

        } else {
            
            // 2. PROSES ABSEN PULANG
            if (!empty($absenHariIni->jam_pulang)) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan Absen Pulang hari ini.']);
            }

            if ($jamSekarang < $jamPulang) {
                return response()->json(['success' => false, 'message' => 'Belum waktunya pulang! Tunggu hingga pukul ' . Carbon::parse($jamPulang)->format('H:i')]);
            }

            try {
                // MENGGUNAKAN METODE SAVE() UNTUK MEMAKSA UPDATE DATABASE
                $absenHariIni->jam_pulang = $jamSekarang;
                $absenHariIni->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Absensi PULANG berhasil dicatat. Hati-hati di jalan! (' . Carbon::parse($jamSekarang)->format('H:i') . ' WIB)'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mencatat! Pastikan Anda sudah menjalankan migration untuk menambah kolom "jam_pulang" di tabel "absensis".'
                ]);
            }
        }
    }
}