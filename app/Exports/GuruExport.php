<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuruExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return User::with('guru')->where('role', 'guru')->orderBy('name', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            ['DATA LENGKAP PROFIL PEGAWAI - SEKOLAH TRI JAYA'],
            ['Diunduh pada: ' . \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB'],
            [], // Empty row for spacing
            ['NO', 'NAMA LENGKAP & GELAR', 'NIK', 'JABATAN', 'UNIT SEKOLAH', 'L/P', 'PENDIDIKAN', 'NO HP', 'ALAMAT']
        ];
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $item->name,
            $item->nik,
            strtoupper(str_replace('_', ' ', $item->jabatan ?? '-')),
            $item->unit_sekolah ? str_replace(',', ', ', $item->unit_sekolah) : 'Umum',
            $item->guru?->jenis_kelamin == 'L' ? 'Laki-laki' : ($item->guru?->jenis_kelamin == 'P' ? 'Perempuan' : '-'),
            $item->guru?->pendidikan_terakhir ?? '-',
            $item->guru?->no_hp ?? '-',
            $item->guru?->alamat ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 14],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF24429B']]
            ],
            2 => [
                'font' => ['size' => 11, 'color' => ['argb' => 'FF333333']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFE8F0FE']]
            ],
            4 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF24429B']]
            ],
        ];
    }
}