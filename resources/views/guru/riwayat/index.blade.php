@extends('layouts.mobile')

@section('title', 'Riwayat Absensi')
@section('subtitle', 'Catatan Kehadiran')
@section('page_title', 'Riwayat Saya')

@section('content')
<div class="space-y-4">

    <!-- Card Ringkasan Singkat (Dinamis sesuai filter) -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#002D8B]/10 text-[#002D8B] flex items-center justify-center">
                <i class="fa-solid fa-clipboard-list text-lg"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800">Total Kehadiran</p>
                <p class="text-xs text-gray-500">Bulan terpilih: <span class="font-bold text-[#002D8B]">{{ $totalHadir }} Hari</span></p>
            </div>
        </div>
    </div>

    <!-- FITUR FILTER BULAN & TAHUN -->
    <form action="{{ route('guru.riwayat') }}" method="GET" class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 flex gap-2 items-end">
        <div class="flex-1">
            <label class="text-[10px] font-semibold text-gray-500 ml-1">Bulan</label>
            <select name="bulan" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-[#002D8B]">
                @foreach(range(1, 12) as $bln)
                    <option value="{{ $bln }}" {{ $bulanSelected == $bln ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($bln)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="text-[10px] font-semibold text-gray-500 ml-1">Tahun</label>
            <select name="tahun" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-[#002D8B]">
                @foreach(range(date('Y')-1, date('Y')+1) as $thn)
                    <option value="{{ $thn }}" {{ $tahunSelected == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-[#002D8B] hover:bg-[#001f63] text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors h-[34px]">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
    </form>

    <!-- List Riwayat -->
    <div class="space-y-3">
        @forelse($riwayatAbsen as $absen)
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center justify-between hover:bg-gray-50 transition-colors">
                
                <!-- Bagian Kiri: Tanggal & Hari -->
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-lg shadow-inner
                        {{ $absen->status == 'Terlambat' ? 'bg-red-50 text-red-500' : ($absen->status == 'Hadir' ? 'bg-green-50 text-green-500' : 'bg-gray-100 text-gray-400') }}">
                        {{ \Carbon\Carbon::parse($absen->tanggal)->format('d') }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('F Y') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l') }}</p>
                    </div>
                </div>
                
                <!-- Bagian Kanan: Jam Masuk & Jam Pulang -->
                <div class="flex-1 flex flex-col justify-center gap-2 pl-4">
                    @if($absen->status == 'Alpa')
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-semibold text-gray-400">Masuk</span>
                            <p class="text-sm font-bold text-gray-400">--:-- WIB</p>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-semibold text-gray-400">Pulang</span>
                            <p class="text-sm font-bold text-gray-400">--:-- WIB</p>
                        </div>
                        <span class="inline-block mt-1 px-2.5 py-1 bg-gray-100 text-gray-500 rounded-md text-[10px] font-bold text-center w-full">
                            Tidak Hadir
                        </span>
                    @else
                        <!-- Baris Masuk -->
                        <div class="flex justify-between items-center bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-100">
                            <span class="text-[10px] font-semibold text-gray-500 w-12">Masuk</span>
                            <div class="flex flex-col items-end">
                                <p class="text-xs font-bold text-gray-800 font-mono">{{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') }} WIB</p>
                                @if($absen->status == 'Terlambat')
                                    @php
                                        $jam = floor($absen->menit_terlambat / 60);
                                        $menit = $absen->menit_terlambat % 60;
                                        $teksTelat = '';
                                        if($jam > 0) $teksTelat .= $jam . ' Jam ';
                                        if($menit > 0) $teksTelat .= $menit . ' Mnt';
                                        if($jam == 0 && $menit == 0) $teksTelat = '0 Mnt';
                                    @endphp
                                    <span class="text-[9px] font-bold text-red-500">Telat {{ $teksTelat }}</span>
                                @else
                                    <span class="text-[9px] font-bold text-green-500">Tepat Waktu</span>
                                @endif
                            </div>
                        </div>

                        <!-- Baris Pulang -->
                        <div class="flex justify-between items-center bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-100">
                            <span class="text-[10px] font-semibold text-gray-500 w-12">Pulang</span>
                            <div class="flex flex-col items-end">
                                @if(!empty($absen->jam_pulang))
                                    <p class="text-xs font-bold text-gray-800 font-mono">{{ \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') }} WIB</p>
                                    <span class="text-[9px] font-bold text-green-500">Selesai</span>
                                @else
                                    <p class="text-xs font-bold text-gray-400 font-mono">--:-- WIB</p>
                                    <span class="text-[9px] font-bold text-yellow-500">Menunggu</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                
            </div>
        @empty
            <!-- Jika Data Kosong -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 text-center mt-2">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-folder-open text-2xl text-gray-300"></i>
                </div>
                <h4 class="text-gray-800 font-bold mb-1">Tidak Ada Data</h4>
                <p class="text-xs text-gray-500">Tidak ada riwayat absensi pada bulan ini.</p>
            </div>
        @endforelse
    </div>

    <!-- PAGINATION (Tombol Next/Prev) -->
    <div class="mt-4 pb-6">
        {{ $riwayatAbsen->links('pagination::tailwind') }}
    </div>

    <!-- Spacer agar tidak tertutup navigasi bawah -->
    <div class="h-10"></div>

</div>
@endsection