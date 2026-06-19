<!DOCTYPE html>
<html>
<head>
    <title>Data Pegawai Sekolah Tri Jaya</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #1a2a40; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #1a2a40; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1a2a40; color: #ffffff; font-weight: bold; text-align: center; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>SEKOLAH TRI JAYA</h1>
        <p>Data Lengkap Pegawai & Guru | Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">NIK</th>
                <th width="18%">Nama Lengkap</th>
                <th width="12%">Jabatan</th>
                <th width="8%">Status</th>
                <th width="5%">L/P</th>
                <th width="15%">TTL</th>
                <th width="10%">No. HP</th>
                <th width="17%">Alamat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gurus as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->nik }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->jabatan }}</td>
                <td class="text-center">{{ $item->guru?->status_pegawai ?? '-' }}</td>
                <td class="text-center">{{ $item->guru?->jenis_kelamin ?? '-' }}</td>
                <td>{{ $item->guru?->tempat_lahir ?? '-' }},<br>{{ $item->guru?->tanggal_lahir ? \Carbon\Carbon::parse($item->guru->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                <td>{{ $item->guru?->no_hp ?? '-' }}</td>
                <td>{{ $item->guru?->alamat ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Belum ada data pegawai.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>