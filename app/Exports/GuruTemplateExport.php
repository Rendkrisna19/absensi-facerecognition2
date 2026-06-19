<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GuruTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            [
                'Budi Santoso', 
                '3201234567890001', 
                'guru', 
                'SD', 
                'L', 
                'Bandung', 
                '1990-01-01', 
                'Islam', 
                '2022-07-01', 
                '081234567890', 
                'S1', 
                'Jl. Merdeka No. 1'
            ],
            [
                'Siti Aminah', 
                '3201234567890002', 
                'kepala_sekolah', 
                'SMP', 
                'P', 
                'Jakarta', 
                '1985-05-15', 
                'Islam', 
                '2015-01-10', 
                '081298765432', 
                'S2', 
                'Jl. Sudirman No. 10'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIK',
            'Jabatan',
            'Unit Sekolah',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Tanggal Bergabung',
            'No HP',
            'Pendidikan Terakhir',
            'Alamat'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply borders to A1:L3 (1 header + 2 sample data rows)
        $sheet->getStyle('A1:L3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Header styling
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF10B981']]
            ],
        ];
    }
}
