<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\IpLokal;
use App\Models\PengaturanAbsensi;
use App\Models\LiburSemester; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AbsensiGuruController extends Controller
{
    // Menampilkan Halaman Beranda (Dashboard)
    public function dashboard()
    {
        $user = auth()->user();
        
        // Atur bahasa waktu ke Indonesia
        Carbon::setLocale('id');
        $hariIni = Carbon::now();
        $bulanIni = $hariIni->month;
        $tahunIni = $hariIni->year;
        $tanggalFormat = $hariIni->translatedFormat('l, d F Y');
        
        // 1. Cek apakah sudah absen hari ini
        $absenHariIni = Absensi::where('user_id', $user->id)
                               ->where('tanggal', $hariIni->format('Y-m-d'))
                               ->first();

        // 2. LOGIKA HARI LIBUR
        $isLibur = false;
        $keteranganLibur = '';

        // ---> TAMBAHAN 2: Cek apakah hari ini termasuk rentang Libur Semester <---
        $liburSemester = LiburSemester::where('is_active', true)
                            ->whereDate('tanggal_mulai', '<=', $hariIni->format('Y-m-d'))
                            ->whereDate('tanggal_selesai', '>=', $hariIni->format('Y-m-d'))
                            ->first();

        if ($liburSemester) {
            // Jika ada libur semester, prioritas libur diatur ke ini
            $isLibur = true;
            $keteranganLibur = 'Libur Semester: ' . $liburSemester->nama_semester;
        } 
        // Lanjut cek hari Minggu jika bukan libur semester
        elseif ($hariIni->isSunday()) {
            $isLibur = true;
            $keteranganLibur = 'Libur Akhir Pekan (Minggu)';
        } 
        // Lanjut cek libur nasional via API
        else {
            try {
                $response = Http::timeout(3)->get('https://dayoffapi.vercel.app/api?month=' . $hariIni->month . '&year=' . $hariIni->year);
                if ($response->successful()) {
                    $liburNasional = $response->json();
                    foreach ($liburNasional as $libur) {
                        if ($libur['tanggal'] === $hariIni->format('Y-m-d') && $libur['is_cuti'] === false) {
                            $isLibur = true;
                            $keteranganLibur = $libur['keterangan'];
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Abaikan jika API mati
            }
        }

        // 3. AMBIL DATA REAL UNTUK KARTU INFORMASI (Bulan Ini)
        $totalHadirBulanIni = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', '!=', 'Alpa')
            ->count();

        // Mengambil nominal denda flat dari pengaturan
        $pengaturan = PengaturanAbsensi::first();
        $nominalDendaFlat = $pengaturan ? $pengaturan->denda_terlambat : 0;

        // Menghitung hari terlambat bulan ini
        $totalTelatBulanIni = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'Terlambat')
            ->count();

        // Total estimasi potongan denda
        $totalDendaBulanIni = $totalTelatBulanIni * $nominalDendaFlat;

        // Kirim semua variabel ke View
        return view('guru.dashboard', compact(
            'absenHariIni', 
            'tanggalFormat', 
            'isLibur', 
            'keteranganLibur',
            'totalHadirBulanIni',
            'totalDendaBulanIni'
        ));
    }

    private function cekJaringanWifi($ipUser)
    {
        $allowedIps = IpLokal::where('is_active', true)->pluck('ip_address');
        foreach ($allowedIps as $allowedIp) {
            $pattern = str_replace('%', '*', $allowedIp);
            if (\Illuminate\Support\Str::is($pattern, $ipUser)) return true;

            $partsAllowed = explode('.', $allowedIp);
            $partsUser = explode('.', $ipUser);
            
            if (count($partsAllowed) === 4 && count($partsUser) === 4) {
                if ($partsAllowed[0] === $partsUser[0] && 
                    $partsAllowed[1] === $partsUser[1]) {
                    return true;
                }
            }

            $ipv6Allowed = explode(':', $allowedIp);
            $ipv6User = explode(':', $ipUser);
            if (count($ipv6Allowed) > 2 && count($ipv6User) > 2) {
                if ($ipv6Allowed[0] === $ipv6User[0]) {
                    return true;
                }
            }
        }
        return false;
    }

    // Menampilkan Halaman Kamera & Validasi IP
    public function scan()
    {
        $user = auth()->user();
        
        // 1. Validasi: Wajah Sudah Terdaftar?
        $wajahTerdaftar = !empty($user->face_descriptor);

        // 2. Validasi: Cek IP Jaringan
        $ipUser = request()->ip();
        $ipValid = $this->cekJaringanWifi($ipUser);

        $pengaturan = PengaturanAbsensi::first();
        $jamSekarang = Carbon::now()->format('H:i:s');
        $hariIni = Carbon::now()->format('Y-m-d');
        
        $isWaktuAbsen = true;
        $pesanWaktu = '';

        // ---> TAMBAHAN 3: Kunci Halaman Scan Jika Libur Semester <---
        $liburSemester = LiburSemester::where('is_active', true)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->first();

        if ($liburSemester) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Saat ini sedang masa ' . $liburSemester->nama_semester . '. Sistem absensi ditutup sementara.';
            return view('guru.scan', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }
        // ---> Selesai Tambahan 3 <---

        // 3. Validasi: CEK WAKTU (Fleksibel & Aman)
        if (!$pengaturan) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Pengaturan jadwal absensi belum dikonfigurasi sama sekali oleh Admin.';
            return view('guru.scan', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        $jamBukaAbsen = $pengaturan->jam_buka_absen ?? '00:00:00'; 
        $jamTutupAbsen = $pengaturan->jam_tutup_absen ?? '23:59:59';

        if ($jamSekarang < $jamBukaAbsen) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Absensi belum dibuka. Anda baru bisa absen mulai pukul ' . Carbon::parse($jamBukaAbsen)->format('H:i') . ' WIB.';
        } elseif ($jamSekarang > $jamTutupAbsen) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Batas waktu absensi masuk telah habis. Tutup pukul ' . Carbon::parse($jamTutupAbsen)->format('H:i') . ' WIB.';
        }

        return view('guru.scan', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
    }

    // Riwayat Absensi
    public function riwayat(Request $request)
    {
        $user = auth()->user();
        Carbon::setLocale('id');

        $bulanSelected = $request->input('bulan', Carbon::now()->month);
        $tahunSelected = $request->input('tahun', Carbon::now()->year);

        $riwayatAbsen = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanSelected)
            ->whereYear('tanggal', $tahunSelected)
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString(); 

        $totalHadir = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanSelected)
            ->whereYear('tanggal', $tahunSelected)
            ->where('status', '!=', 'Alpa')
            ->count();

        return view('guru.riwayat', compact('riwayatAbsen', 'bulanSelected', 'tahunSelected', 'totalHadir'));
    }

    // Denda Keterlambatan
    public function denda(Request $request)
    {
        $user = auth()->user();
        Carbon::setLocale('id');

        $bulanSelected = $request->input('bulan', Carbon::now()->month);
        $tahunSelected = $request->input('tahun', Carbon::now()->year);

        $pengaturan = PengaturanAbsensi::first();
        $nominalDendaFlat = $pengaturan ? $pengaturan->denda_terlambat : 0;

        $riwayatTerlambat = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanSelected)
            ->whereYear('tanggal', $tahunSelected)
            ->where('status', 'Terlambat')
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalHariTelat = $riwayatTerlambat->count();
        $totalDenda = $totalHariTelat * $nominalDendaFlat;

        $namaBulanTahun = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->translatedFormat('F Y');

        return view('guru.denda', compact(
            'riwayatTerlambat', 
            'totalHariTelat', 
            'totalDenda', 
            'nominalDendaFlat',
            'bulanSelected',
            'tahunSelected',
            'namaBulanTahun'
        ));
    }

    // Halaman Pengaturan Profil
    public function pengaturan()
    {
        $user = auth()->user();
        return view('guru.pengaturan', compact('user'));
    }

    // Update Profil Wajah/Foto
    public function updateProfil(Request $request)
    {
        $request->validate([
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' 
        ]);

        $user = auth()->user();

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::exists('public/' . $user->foto_profil)) {
                Storage::delete('public/' . $user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profil', 'public');
            $user->update(['foto_profil' => $path]);
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    // Menyimpan Absensi via AJAX
    public function storeAbsensi(Request $request)
    {
        $user = auth()->user();
        $hariIni = Carbon::now()->format('Y-m-d');
        $jamSekarang = Carbon::now()->format('H:i:s');
        
        // ---> TAMBAHAN JARINGAN WIFI <---
        $ipUser = request()->ip();
        if (!$this->cekJaringanWifi($ipUser)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Anda tidak terhubung ke WiFi sekolah.'
            ]);
        }
        
        // ---> TAMBAHAN 4: Kunci API Backend Jika Libur Semester <---
        $liburSemester = LiburSemester::where('is_active', true)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->first();

        if ($liburSemester) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Saat ini sedang masa ' . $liburSemester->nama_semester . '. Sistem ditutup.'
            ]);
        }
        // ---> Selesai Tambahan 4 <---

        $pengaturan = PengaturanAbsensi::first();

        if (!$pengaturan) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Pengaturan jadwal absensi belum dikonfigurasi.'
            ]);
        }

        // Ambil data asli, fallback ke nilai aman jika database NULL
        $jamBukaAbsen = $pengaturan->jam_buka_absen ?? '00:00:00'; 
        $jamTutupAbsen = $pengaturan->jam_tutup_absen ?? '23:59:59';
        $batasMasuk    = $pengaturan->batas_jam_masuk ?? '07:15:00';
        
        if ($jamSekarang < $jamBukaAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi belum dibuka. Silakan kembali pada pukul ' . Carbon::parse($jamBukaAbsen)->format('H:i') . ' WIB.'
            ]);
        }

        if ($jamSekarang > $jamTutupAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu absensi untuk hari ini sudah ditutup.'
            ]);
        }
        
        $sudahAbsen = Absensi::where('user_id', $user->id)
                             ->where('tanggal', $hariIni)
                             ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi hari ini.'
            ]);
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
            'message' => 'Absensi berhasil dicatat sebagai ' . $status . ' pada ' . Carbon::parse($jamSekarang)->format('H:i') . ' WIB.'
        ]);
    }
}