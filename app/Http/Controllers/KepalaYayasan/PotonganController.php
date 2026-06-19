<?php

namespace App\Http\Controllers\KepalaYayasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\PengaturanAbsensi;
use App\Services\AlpaService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PotonganExport;

class PotonganController extends Controller
{
    // Fungsi Helper Khusus agar tidak mengulang kodingan
    private function getPotonganData($bulan, $tahun, $unitSekolah = null)
    {
        $pengaturan = PengaturanAbsensi::first();
        $nominalDenda = $pengaturan ? $pengaturan->denda_terlambat : 0;
        
        $query = User::with('guru')->where('role', 'guru');
        if ($unitSekolah && $unitSekolah !== 'Semua') {
            $query->where('unit_sekolah', $unitSekolah);
        }
        $gurus = $query->orderBy('name', 'asc')->get();

        $dataPotongan = [];
        $totalKeseluruhanPotongan = 0;
        $totalGuruDipotong = 0;

        foreach ($gurus as $guru) {
            // Count Terlambat records
            $riwayatTelat = Absensi::where('user_id', $guru->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status', 'Terlambat')
                ->orderBy('tanggal', 'asc')
                ->get();

            // Count Alpa records
            $riwayatAlpa = Absensi::where('user_id', $guru->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status', 'Alpa')
                ->orderBy('tanggal', 'asc')
                ->get();

            $jumlahTelat = $riwayatTelat->count();
            $jumlahAlpa = $riwayatAlpa->count();
            $totalPotongan = ($jumlahTelat + $jumlahAlpa) * $nominalDenda;

            if (($jumlahTelat + $jumlahAlpa) > 0) $totalGuruDipotong++;
            $totalKeseluruhanPotongan += $totalPotongan;

            // Merge riwayat for display
            $riwayatGabungan = $riwayatTelat->merge($riwayatAlpa)->sortBy('tanggal')->values();

            $dataPotongan[] = (object)[
                'id' => $guru->id,
                'name' => $guru->name,
                'nik' => $guru->nik,
                'jabatan' => $guru->jabatan,
                'foto' => $guru->foto_profil,
                'jumlah_telat' => $jumlahTelat,
                'jumlah_alpa' => $jumlahAlpa,
                'total_potongan' => $totalPotongan,
                'riwayat' => $riwayatGabungan
            ];
        }

        usort($dataPotongan, function($a, $b) {
            return $b->total_potongan <=> $a->total_potongan;
        });

        return [
            'data' => $dataPotongan,
            'total_potongan' => $totalKeseluruhanPotongan,
            'total_guru' => $totalGuruDipotong,
            'nominal_denda' => $nominalDenda
        ];
    }

    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $bulanSelected = $request->input('bulan', Carbon::now()->month);
        $tahunSelected = $request->input('tahun', Carbon::now()->year);
        $unitSekolah = $request->input('unit_sekolah', 'Semua');

        // Backfill Alpa records for the selected month
        $startDate = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->endOfMonth()->format('Y-m-d');
        AlpaService::backfillAlpaRecords($startDate, $endDate);

        $laporan = $this->getPotonganData($bulanSelected, $tahunSelected, $unitSekolah);
        $namaBulanTahun = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->translatedFormat('F Y');

        return view('kepala-yayasan.potongan.index', [
            'dataPotongan' => $laporan['data'],
            'totalKeseluruhanPotongan' => $laporan['total_potongan'],
            'totalGuruDipotong' => $laporan['total_guru'],
            'nominalDenda' => $laporan['nominal_denda'],
            'bulanSelected' => $bulanSelected,
            'tahunSelected' => $tahunSelected,
            'namaBulanTahun' => $namaBulanTahun,
            'unitSekolah' => $unitSekolah
        ]);
    }

    // Method Export PDF
    public function exportPdf(Request $request)
    {
        Carbon::setLocale('id');
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $unitSekolah = $request->input('unit_sekolah');

        // Backfill Alpa records
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');
        AlpaService::backfillAlpaRecords($startDate, $endDate);
        
        $laporan = $this->getPotonganData($bulan, $tahun, $unitSekolah);
        $namaBulanTahun = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');

        $pdf = Pdf::loadView('kepala-yayasan.potongan.pdf', [
            'dataPotongan' => $laporan['data'],
            'totalKeseluruhan' => $laporan['total_potongan'],
            'namaBulanTahun' => $namaBulanTahun
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Potongan_Gaji_' . $namaBulanTahun . '.pdf');
    }

    // Method Export Excel
    public function exportExcel(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $unitSekolah = $request->input('unit_sekolah');

        // Backfill Alpa records
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');
        AlpaService::backfillAlpaRecords($startDate, $endDate);

        $namaBulanTahun = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');

        return Excel::download(new PotonganExport($bulan, $tahun, $unitSekolah), 'Laporan_Potongan_Gaji_'.$namaBulanTahun.'.xlsx');
    }
}