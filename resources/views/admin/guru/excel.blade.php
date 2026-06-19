<table>
    <thead>
        <tr>
            <th colspan="9" style="text-align: center; font-size: 16px; font-weight: bold; background-color: #24429b; color: #ffffff;">DATA LENGKAP PROFIL PEGAWAI - SEKOLAH TRI JAYA</th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center; font-size: 11px; background-color: #e8f0fe; color: #333333;">Diunduh pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</th>
        </tr>
        <tr></tr> <!-- Empty row for spacing -->
        <tr>
            <th style="background-color: #24429b; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; width: 5px;">NO</th>
            <th style="background-color: #24429b; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; width: 25px;">NAMA LENGKAP & GELAR</th>
            <th style="background-color: #24429b; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; width: 18px;">NIK</th>
            <th style="background-color: #24429b; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; width: 15px;">JABATAN</th>
            <th style="background-color: #24429b; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; width: 15px;">UNIT SEKOLAH</th>
            <th style="background-color: #24429b; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; width: 15px;">L/P</th>
            <th style="background-color: #24429b; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; width: 20px;">PENDIDIKAN</th>
            <th style="background-color: #24429b; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; width: 15px;">NO HP</th>
            <th style="background-color: #24429b; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; width: 35px;">ALAMAT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($gurus as $index => $item)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000; font-weight: bold;">{{ $item->name }}</td>
            <td style="border: 1px solid #000000;">{{ $item->nik }}</td>
            <td style="border: 1px solid #000000;">{{ strtoupper(str_replace('_', ' ', $item->jabatan ?? '-')) }}</td>
            <td style="border: 1px solid #000000;">{{ $item->unit_sekolah ? str_replace(',', ', ', $item->unit_sekolah) : 'Umum' }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $item->guru?->jenis_kelamin == 'L' ? 'Laki-laki' : ($item->guru?->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
            <td style="border: 1px solid #000000;">{{ $item->guru?->pendidikan_terakhir ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $item->guru?->no_hp ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $item->guru?->alamat ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>