<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background-color: #1e40af; color: white; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2 class="text-center">Riwayat Absensi Guru Keseluruhan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Guru</th>
                <th>NIK</th>
                <th>Unit Sekolah</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Jam Masuk</th>
                <th class="text-center">Jam Pulang</th>
                <th class="text-center">Keterlambatan</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayat as $i => $absen)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $absen->user->name ?? 'Terhapus' }}</td>
                <td>{{ $absen->user->nik ?? '-' }}</td>
                <td>{{ $absen->user->unit_sekolah ?? 'Umum' }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}</td>
                <td class="text-center">{{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '-' }}</td>
                <td class="text-center">{{ $absen->jam_pulang ? \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') : '-' }}</td>
                @php
                    $jam = floor($absen->menit_terlambat / 60);
                    $menit = $absen->menit_terlambat % 60;
                    $teksTelat = '';
                    if($jam > 0) $teksTelat .= $jam . ' jam ';
                    if($menit > 0) $teksTelat .= $menit . ' mnt';
                @endphp
                <td class="text-center">{{ $absen->menit_terlambat > 0 ? trim($teksTelat) : '-' }}</td>
                <td class="text-center">{{ $absen->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
