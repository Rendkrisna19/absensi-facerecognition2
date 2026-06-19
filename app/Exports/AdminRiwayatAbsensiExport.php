<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AdminRiwayatAbsensiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search, $status, $bulan, $tahun, $unitSekolah;

    public function __construct($search, $status, $bulan, $tahun, $unitSekolah)
    {
        $this->search = $search;
        $this->status = $status;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->unitSekolah = $unitSekolah;
    }

    public function collection()
    {
        $query = Absensi::with('user')->orderBy('tanggal', 'desc')->orderBy('jam_masuk', 'desc');

        if (!empty($this->search)) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('nik', 'like', "%{$this->search}%");
            });
        }

        if (!empty($this->status) && $this->status != 'all') {
            $query->where('status', $this->status);
        }

        if (!empty($this->bulan) && $this->bulan != 'all') {
            $query->whereMonth('tanggal', $this->bulan);
        }

        if (!empty($this->tahun) && $this->tahun != 'all') {
            $query->whereYear('tanggal', $this->tahun);
        }

        if (!empty($this->unitSekolah) && $this->unitSekolah != 'all') {
            $query->whereHas('user', function($q) {
                $q->where('unit_sekolah', $this->unitSekolah);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Guru', 'NIK', 'Unit Sekolah', 'Jam Masuk', 'Jam Pulang', 'Keterlambatan', 'Status'];
    }

    public function map($absen): array
    {
        static $no = 0;
        $no++;
        
        Carbon::setLocale('id');
        $tanggal = Carbon::parse($absen->tanggal)->translatedFormat('d F Y');
        
        $jamMasuk = $absen->jam_masuk ? Carbon::parse($absen->jam_masuk)->format('H:i') : '-';
        $jamPulang = $absen->jam_pulang ? Carbon::parse($absen->jam_pulang)->format('H:i') : '-';
        
        $telat = '-';
        if ($absen->menit_terlambat > 0) {
            $jam = floor($absen->menit_terlambat / 60);
            $menit = $absen->menit_terlambat % 60;
            $teksTelat = '';
            if($jam > 0) $teksTelat .= $jam . ' Jam ';
            if($menit > 0) $teksTelat .= $menit . ' Menit';
            $telat = trim($teksTelat);
        }

        return [
            $no,
            $tanggal,
            $absen->user->name ?? 'Terhapus',
            $absen->user->nik ?? '-',
            $absen->user->unit_sekolah ?? 'Umum',
            $jamMasuk,
            $jamPulang,
            $telat,
            $absen->status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E40AF']] // Blue-800
            ],
        ];
    }
}
