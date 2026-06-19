<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #1e40af; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #1e40af; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 13px; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1e40af; color: #ffffff; text-transform: uppercase; font-size: 10px; }
        
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-red { color: #dc2626; font-weight: bold; }
        .text-green { color: #16a34a; font-weight: bold; }
        .text-orange { color: #ea580c; font-weight: bold; }
        
        .footer { margin-top: 40px; text-align: right; font-size: 12px;}
    </style>
</head>
<body>

    <div class="header">
        <h2>YAYASAN TRI JAYA</h2>
        <p>LAPORAN RINCIAN KEHADIRAN GURU</p>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="30%">Nama Guru</th>
                <th class="text-center" width="15%">Jam Masuk</th>
                <th class="text-center" width="15%">Keterlambatan</th>
                <th class="text-center" width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; \Carbon\Carbon::setLocale('id'); @endphp
            @forelse($absensis as $absen)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d F Y') }}<br>
                        <small style="color:#666">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l') }}</small>
                    </td>
                    <td>
                        <strong>{{ $absen->user->name ?? 'User Terhapus' }}</strong><br>
                        <small style="color:#666">{{ $absen->user->jabatan ?? '-' }}</small>
                    </td>
                    <td class="text-center">
                        {{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '-' }}
                    </td>
                    <td class="text-center">
                        @if($absen->menit_terlambat > 0)
                            <span class="text-red">+{{ $absen->menit_terlambat }} Mnt</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($absen->status == 'Hadir')
                            <span class="text-green">Tepat Waktu</span>
                        @elseif($absen->status == 'Terlambat')
                            <span class="text-orange">Terlambat</span>
                        @else
                            <span class="text-red">Alpa</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data absensi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Medan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <br><br><br>
        <p><strong>Administrator / Kepala Yayasan</strong></p>
    </div>

</body>
</html>