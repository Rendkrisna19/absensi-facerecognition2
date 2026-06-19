<?php

namespace App\Http\Controllers;

use App\Models\IpLokal;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    /**
     * Tampilkan halaman QR Code absensi (tanpa login).
     * QR Code otomatis ambil IP dari host yang dipakai untuk akses halaman ini.
     */
    public function index(Request $request)
    {
        // 1. Ambil host yang dipakai untuk akses halaman ini
        //    Ini otomatis dapat IP LAN yang benar saat diakses dari HP guru
        $host = $request->getHost();

        // Validasi: pastikan host bukan localhost / bukan hostname tanpa IP
        if ($host === '127.0.0.1' || $host === '::1' || $host === 'localhost') {
            // Diakses dari PC admin langsung — coba ambil IP LAN asli
            $serverIp = getHostByName(getHostName());
            if (!$serverIp || $serverIp === '127.0.0.1') {
                $serverIp = $host; // fallback
            }
        } elseif (filter_var($host, FILTER_VALIDATE_IP)) {
            // Diakses dari HP / device lain via IP — ini yang kita mau!
            $serverIp = $host;
        } else {
            // Hostname (bukan IP) — coba resolve ke IP
            $serverIp = getHostByName($host);
            if (!$serverIp || $serverIp === $host) {
                $serverIp = getHostByName(getHostName());
            }
        }

        // 2. Tentukan port
        $port = $request->getPort();
        $portStr = ($port === 80 || $port === 443) ? '' : ':' . $port;

        // 3. Generate URL absensi
        $absensiUrl = "http://{$serverIp}{$portStr}/guru/scan-absensi";

        // 4. Jumlah jaringan terdaftar (untuk info saja)
        $totalJaringan = IpLokal::where('is_active', true)->count();

        return view('qr-absensi', [
            'serverIp' => $serverIp,
            'absensiUrl' => $absensiUrl,
            'totalJaringan' => $totalJaringan,
        ]);
    }
}
