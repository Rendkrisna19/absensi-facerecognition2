<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Absensi;
use App\Models\PengaturanAbsensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PotonganExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $bulan, $tahun, $unitSekolah;

    public function __construct($bulan, $tahun, $unitSekolah = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->unitSekolah = $unitSekolah;
    }

    public function collection()
    {
        $nominalDenda = PengaturanAbsensi::first()->denda_terlambat ?? 0;
        $query = User::where('role', 'guru');
        
        if ($this->unitSekolah && $this->unitSekolah !== 'Semua') {
            $query->where('unit_sekolah', $this->unitSekolah);
        }
        
        $gurus = $query->orderBy('name', 'asc')->get();
        $data = [];

        foreach ($gurus as $guru) {
            $jumlahTelat = Absensi::where('user_id', $guru->id)
                ->whereMonth('tanggal', $this->bulan)
                ->whereYear('tanggal', $this->tahun)
                ->where('status', 'Terlambat')
                ->count();

            $jumlahAlpa = Absensi::where('user_id', $guru->id)
                ->whereMonth('tanggal', $this->bulan)
                ->whereYear('tanggal', $this->tahun)
                ->where('status', 'Alpa')
                ->count();

            if (($jumlahTelat + $jumlahAlpa) > 0) {
                $data[] = (object)[
                    'nik' => $guru->nik,
                    'name' => $guru->name,
                    'jabatan' => $guru->jabatan,
                    'jumlah_telat' => $jumlahTelat,
                    'jumlah_alpa' => $jumlahAlpa,
                    'potongan' => ($jumlahTelat + $jumlahAlpa) * $nominalDenda
                ];
            }
        }
        
        // Urutkan potongan terbesar ke atas
        usort($data, fn($a, $b) => $b->potongan <=> $a->potongan);
        return collect($data);
    }

    public function headings(): array
    {
        return ['No', 'NIK', 'Nama Guru', 'Jabatan', 'Total Telat (Hari)', 'Total Alpa (Hari)', 'Total Potongan (Rp)'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [$no, $row->nik, $row->name, $row->jabatan, $row->jumlah_telat, $row->jumlah_alpa, $row->potongan];
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