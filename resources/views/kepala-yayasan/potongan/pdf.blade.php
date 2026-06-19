<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Potongan Gaji</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e40af; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #1e40af; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        /* Warna Header Biru 800 */
        th { background-color: #1e40af; color: #ffffff; text-transform: uppercase; font-size: 11px; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-red { color: #dc2626; }
        
        .total-row { background-color: #f3f4f6; }
        .footer { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h2>YAYASAN TRI JAYA</h2>
        <p>REKAPITULASI PEMOTONGAN GAJI (KETERLAMBATAN & KETIDAK-HADIRAN)</p>
        <p>Periode: <strong>{{ $namaBulanTahun }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="18%">NIK</th>
                <th width="30%">Nama Lengkap</th>
                <th class="text-center" width="12%">Frekuensi Telat</th>
                <th class="text-center" width="12%">Tidak Absen</th>
                <th class="text-right" width="23%">Total Potongan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($dataPotongan as $data)
                @if($data->total_potongan > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $data->nik }}<br><small style="color: #666;">{{ $data->jabatan }}</small></td>
                    <td class="font-bold">{{ $data->name }}</td>
                    <td class="text-center">{{ $data->jumlah_telat }} Hari</td>
                    <td class="text-center">{{ $data->jumlah_alpa ?? 0 }} Hari</td>
                    <td class="text-right font-bold text-red">Rp {{ number_format($data->total_potongan, 0, ',', '.') }}</td>
                </tr>
                @endif
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pemotongan gaji di bulan ini.</td>
                </tr>
            @endforelse
            
            @if($totalKeseluruhan > 0)
            <tr class="total-row">
                <td colspan="5" class="text-right font-bold">TOTAL KESELURUHAN POTONGAN</td>
                <td class="text-right font-bold text-red">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Medan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <br><br><br>
        <p><strong>Kepala Yayasan</strong></p>
    </div>

</body>
</html>