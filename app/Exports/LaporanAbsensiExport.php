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

class LaporanAbsensiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate, $endDate, $guruId, $unitSekolah;

    public function __construct($startDate, $endDate, $guruId, $unitSekolah = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->guruId = $guruId;
        $this->unitSekolah = $unitSekolah;
    }

    public function collection()
    {
        $query = Absensi::with('user')->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        
        if ($this->guruId) {
            $query->where('user_id', $this->guruId);
        }

        if ($this->unitSekolah && $this->unitSekolah !== 'Semua') {
            $query->whereHas('user', function($q) {
                $q->where('unit_sekolah', $this->unitSekolah);
            });
        }

        return $query->orderBy('tanggal', 'asc')->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Hari', 'Nama Guru', 'Jabatan', 'Jam Masuk', 'Keterlambatan', 'Status'];
    }

    public function map($absen): array
    {
        static $no = 0;
        $no++;
        
        Carbon::setLocale('id');
        $tanggal = Carbon::parse($absen->tanggal)->translatedFormat('d F Y');
        $hari = Carbon::parse($absen->tanggal)->translatedFormat('l');
        
        $jamMasuk = $absen->jam_masuk ? Carbon::parse($absen->jam_masuk)->format('H:i') : '-';
        $telat = $absen->menit_terlambat > 0 ? $absen->menit_terlambat . ' Menit' : '-';

        return [
            $no,
            $tanggal,
            $hari,
            $absen->user->name ?? 'Terhapus',
            $absen->user->jabatan ?? '-',
            $jamMasuk,
            $telat,
            $absen->status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header: Warna Biru 800 (#1e40af), Text Putih
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E40AF']]
            ],
        ];
    }
}