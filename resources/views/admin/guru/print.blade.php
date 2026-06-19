<!DOCTYPE html>
<html>
<head>
    <title>Profil Pegawai - {{ $guru->name }}</title>
    <style>
        /* Base Reset & Typography */
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #333; 
            line-height: 1.5; 
            padding: 0; 
            margin: 0; 
            background-color: #fff;
        }
        .container { 
            padding: 40px; 
        }

        /* Header Layout (PDF Friendly Table) */
        .header-table {
            width: 100%;
            border-bottom: 4px solid #24429b;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-logo {
            width: 15%;
            text-align: left;
        }
        .header-logo img {
            width: 80px; 
            max-height: 80px;
            object-fit: contain;
        }
        .header-title {
            width: 70%;
            text-align: center;
        }
        .header-title h1 { 
            margin: 0; 
            color: #24429b; 
            font-size: 26px; 
            letter-spacing: 1.5px; 
            font-weight: bold; 
            text-transform: uppercase; 
        }
        .header-title p { 
            margin: 5px 0 0; 
            font-size: 14px; 
            font-weight: bold; 
            color: #666; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
        }
        .header-balancer {
            width: 15%; /* Penyeimbang agar judul tetap tepat di tengah */
        }
        
        /* Section Titles */
        .section-title { 
            background-color: #e8f0fe; 
            color: #24429b; 
            padding: 10px 15px; 
            font-weight: bold; 
            font-size: 15px; 
            margin-bottom: 15px; 
            border-left: 6px solid #24429b;
            text-transform: uppercase;
            border-radius: 0 4px 4px 0;
        }
        
        /* Layout Grids */
        .layout-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .layout-info { width: 75%; vertical-align: top; padding-right: 25px; }
        .layout-photo { width: 25%; vertical-align: top; text-align: center; }
        
        /* Photo Styling */
        .photo-container {
            padding-top: 15px;
        }
        .photo-frame { 
            width: 130px; 
            height: 170px; 
            border: 3px solid #24429b; 
            border-radius: 8px;
            padding: 3px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            object-fit: cover; 
            display: inline-block;
            background-color: #f9f9f9;
        }
        
        /* Data Tables */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .data-table td { padding: 10px 12px; font-size: 14px; }
        .data-table tr:nth-child(even) { background-color: #fcfcfc; } /* Zebra striping */
        .data-table tr { border-bottom: 1px solid #f0f0f0; }
        
        .data-table td.label { width: 35%; font-weight: bold; color: #444; }
        .data-table td.separator { width: 2%; text-align: center; font-weight: bold; color: #999; }
        .data-table td.value { width: 63%; color: #111; }
        
        /* Badges */
        .badge {
            background: #24429b;
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(36, 66, 155, 0.2);
        }
        
        .badge-success { background: #198754; }
        .badge-danger { background: #dc3545; }

        /* Footer & Signatures */
        .footer { 
            margin-top: 50px; 
            border-top: 2px dashed #ddd; 
            padding-top: 30px; 
        }
        .print-info {
            float: left;
            font-size: 12px;
            color: #777;
            margin-top: 40px;
        }
        .signature-box { 
            float: right; 
            width: 220px; 
            text-align: center; 
        }
        .signature-line { 
            border-bottom: 1px solid #222; 
            margin-top: 80px; 
            margin-bottom: 5px; 
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <!-- Memanggil gambar fisik logo dari public/images/logo.png -->
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" alt="Logo Sekolah">
                    @else
                        <!-- Fallback jika gambar hilang -->
                        <div style="background: #24429b; color: white; width: 60px; height: 60px; border-radius: 50%; line-height: 60px; text-align: center; font-weight: bold; font-size: 18px;">STJ</div>
                    @endif
                </td>
                <td class="header-title">
                    <h1>SEKOLAH TRI JAYA</h1>
                    <p>Data Lengkap Profil Pegawai</p>
                </td>
                <td class="header-balancer"></td>
            </tr>
        </table>

        <!-- BAGIAN 1: INFO UTAMA & FOTO -->
        <table class="layout-table">
            <tr>
                <td class="layout-info">
                    <div class="section-title">Informasi Utama</div>
                    <table class="data-table">
                        <tr>
                            <td class="label">Nomor Induk (NIK)</td>
                            <td class="separator">:</td>
                            <td class="value" style="font-weight: bold; color: #24429b;">{{ $guru->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Nama Lengkap & Gelar</td>
                            <td class="separator">:</td>
                            <td class="value" style="font-size: 15px; font-weight: bold;">{{ $guru->name }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jabatan</td>
                            <td class="separator">:</td>
                            <td class="value">
                                <span class="badge">{{ strtoupper(str_replace('_', ' ', $guru->jabatan ?? 'GURU')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Unit Sekolah</td>
                            <td class="separator">:</td>
                            <td class="value">
                                <strong>{{ $guru->unit_sekolah ? str_replace(',', ', ', $guru->unit_sekolah) : 'Umum' }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Status Wajah (Absensi)</td>
                            <td class="separator">:</td>
                            <td class="value">
                                @if($guru->face_descriptor)
                                    <span class="badge badge-success">✓ Sudah Direkam</span>
                                @else
                                    <span class="badge badge-danger">✗ Belum Direkam</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                
                <td class="layout-photo">
                    <div class="photo-container">
                        @if($guru->foto_profil && file_exists(public_path('storage/' . $guru->foto_profil)))
                            <img src="{{ public_path('storage/' . $guru->foto_profil) }}" class="photo-frame" alt="Foto Profil">
                        @else
                            <div class="photo-frame" style="line-height: 170px; color: #aaa; font-size: 13px; font-style: italic;">
                                Tidak Ada Foto
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- BAGIAN 2: BIODATA PRIBADI -->
        <div class="section-title">Biodata Pribadi</div>
        <table class="data-table">
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="separator">:</td>
                <td class="value">
                    {{ $guru->guru?->tempat_lahir ?? '-' }}, 
                    {{ $guru->guru?->tanggal_lahir ? \Carbon\Carbon::parse($guru->guru->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="separator">:</td>
                <td class="value">
                    @if($guru->guru?->jenis_kelamin == 'L')
                        Laki-laki
                    @elseif($guru->guru?->jenis_kelamin == 'P')
                        Perempuan
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Agama</td>
                <td class="separator">:</td>
                <td class="value">{{ $guru->guru?->agama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Pendidikan Terakhir</td>
                <td class="separator">:</td>
                <td class="value">{{ $guru->guru?->pendidikan_terakhir ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Handphone</td>
                <td class="separator">:</td>
                <td class="value">{{ $guru->guru?->no_hp ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Bergabung</td>
                <td class="separator">:</td>
                <td class="value">{{ $guru->guru?->tanggal_bergabung ? \Carbon\Carbon::parse($guru->guru->tanggal_bergabung)->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="vertical-align: top;">Alamat Lengkap</td>
                <td class="separator" style="vertical-align: top;">:</td>
                <td class="value" style="line-height: 1.6;">{{ $guru->guru?->alamat ?? '-' }}</td>
            </tr>
        </table>

        <!-- BAGIAN 3: FOOTER & TTD -->
        <div class="footer clearfix">
            <div class="print-info">
                <p style="margin: 0; font-weight: bold; color: #555;">Sistem Informasi Manajemen Tri Jaya</p>
                <p style="margin: 3px 0 0;">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>

            <div class="signature-box">
                <p style="margin:0; font-size: 14px;">Mengetahui,</p>
                <p style="margin:5px 0 0; font-size: 15px;"><strong>Kepala HRD / Yayasan</strong></p>
                <div class="signature-line"></div>
                <p style="margin:0; font-size: 12px; color: #555; font-style: italic;">(Nama Lengkap & Gelar)</p>
            </div>
        </div>
        
    </div>
</body>
</html>