@extends('layouts.mobile') @section('title', 'Beranda')
@section('subtitle', 'Halo, ' . $greeting)
@section('page_title', auth()->user()->name)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between px-1">
        <div>
            <h2 class="text-gray-800 font-bold text-lg">Hari Ini</h2>
            <p class="text-gray-500 text-sm flex items-center gap-1.5">
                <i class="fa-regular fa-calendar text-[#002D8B]"></i> 
                {{ $tanggalFormat }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 px-3 py-1.5 rounded-xl text-sm font-bold text-[#002D8B] shadow-sm flex items-center gap-2" id="realtime-clock">
            <i class="fa-regular fa-clock"></i> <span>--:--</span>
        </div>
    </div>

    @if($izinHariIni)
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden border border-amber-400">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10 text-center py-4">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <i class="fa-solid fa-envelope-open-text text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold mb-1">Status: {{ $izinHariIni->jenis }}</h2>
                <p class="text-amber-100 text-sm font-medium bg-black/10 inline-block px-3 py-1 rounded-full mt-2">
                    {{ $izinHariIni->status == 'Pending' ? 'Menunggu Persetujuan' : 'Telah Disetujui' }}
                </p>
                <p class="text-xs text-amber-50 mt-4 px-4 line-clamp-2 italic">"{{ $izinHariIni->alasan }}"</p>
            </div>
        </div>

    @elseif($isLibur)
        <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden border border-teal-400">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10 text-center py-4">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <i class="fa-solid fa-mug-hot text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold mb-1">Hari Libur</h2>
                <p class="text-teal-100 text-sm font-medium bg-black/10 inline-block px-3 py-1 rounded-full mt-2">
                    {{ $keteranganLibur }}
                </p>
                <p class="text-xs text-teal-100 mt-4">Selamat beristirahat, tidak perlu melakukan absensi hari ini.</p>
            </div>
        </div>

    @else
        <div class="bg-[#002D8B] rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10">
                <div class="mb-5">
                    <p class="text-sm text-blue-200 font-medium mb-1">Status Kehadiran Anda</p>
                    
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        @if(!$absenHariIni)
                            <i class="fa-solid fa-circle-exclamation text-orange-400"></i> Belum Absen Masuk
                        @elseif(empty($absenHariIni->jam_pulang))
                            <i class="fa-solid fa-spinner fa-spin text-yellow-300"></i> Menunggu Waktu Pulang
                        @else
                            <i class="fa-solid fa-circle-check text-green-400"></i> Kehadiran Selesai
                        @endif
                    </h2>
                </div>

                @if(!$absenHariIni)
                    <a href="{{ route('guru.scan') }}" class="flex items-center justify-center gap-3 w-full bg-orange-400 hover:bg-orange-500 text-white font-bold py-3.5 rounded-xl transition-all shadow-md active:scale-95">
                        <i class="fa-solid fa-right-to-bracket text-xl"></i> Scan Absen Masuk
                    </a>
                    <p class="text-center text-xs text-blue-200 mt-3"><i class="fa-solid fa-circle-info mr-1"></i> Pastikan Anda terhubung dengan WiFi sekolah.</p>

                @else
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-white/10 rounded-xl p-3 backdrop-blur-md border border-white/10">
                            <p class="text-[10px] text-blue-200 mb-1 uppercase tracking-wider">Jam Masuk</p>
                            <p class="font-bold text-lg font-mono">{{ \Carbon\Carbon::parse($absenHariIni->jam_masuk)->format('H:i') }}</p>
                            @if($absenHariIni->status == 'Terlambat')
                                <span class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded mt-1 inline-block">Terlambat</span>
                            @else
                                <span class="bg-green-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded mt-1 inline-block">Tepat Waktu</span>
                            @endif
                        </div>
                        
                        <div class="bg-white/10 rounded-xl p-3 backdrop-blur-md border border-white/10">
                            <p class="text-[10px] text-blue-200 mb-1 uppercase tracking-wider">Jam Pulang</p>
                            @if(!empty($absenHariIni->jam_pulang))
                                <p class="font-bold text-lg font-mono">{{ \Carbon\Carbon::parse($absenHariIni->jam_pulang)->format('H:i') }}</p>
                                <span class="bg-green-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded mt-1 inline-block">Selesai</span>
                            @else
                                <p class="font-bold text-lg font-mono text-gray-400">--:--</p>
                                <span class="bg-yellow-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded mt-1 inline-block">Menunggu</span>
                            @endif
                        </div>
                    </div>

                    @if(empty($absenHariIni->jam_pulang))
                        <div class="bg-blue-900/40 rounded-xl p-3 border border-blue-400/30 text-center mb-4">
                            <p class="text-xs text-blue-100">
                                <i class="fa-solid fa-clock mr-1"></i> Absensi Pulang akan dibuka pukul <strong class="text-white">{{ \Carbon\Carbon::parse($jamPulang)->format('H:i') }} WIB</strong>.
                            </p>
                        </div>
                        
                        <a href="{{ route('guru.scan') }}" class="flex items-center justify-center gap-2 w-full bg-white text-[#002D8B] font-bold py-3.5 rounded-xl transition-all shadow-md active:scale-95">
                            <i class="fa-solid fa-person-walking-arrow-right text-lg"></i> Scan Absen Pulang
                        </a>
                    @endif
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex flex-col justify-between h-28 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-green-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-2 relative z-10">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[11px] text-gray-500 font-semibold mb-0.5 uppercase tracking-wide">Hadir Bulan Ini</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $totalHadirBulanIni }} <span class="text-sm font-normal text-gray-400">Hari</span></h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex flex-col justify-between h-28 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-red-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <i class="fa-solid fa-rupiah-sign absolute -right-2 -bottom-2 text-5xl text-red-50 opacity-50"></i>
            <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-2 relative z-10">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[11px] text-gray-500 font-semibold mb-0.5 uppercase tracking-wide">Potongan Gaji</p>
                <h4 class="text-lg font-bold text-red-600">Rp {{ number_format($totalDendaBulanIni, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="mt-8 mb-2 flex items-center justify-between px-1">
        <h3 class="text-gray-800 font-bold text-base">Riwayat Izin Terbaru</h3>
        <a href="{{ route('guru.pengajuan-izin.index') }}" class="text-[11px] text-[#002D8B] font-semibold hover:underline">Lihat Semua</a>
    </div>

    <div class="space-y-3">
        @forelse($riwayatIzin as $izin)
            <div class="bg-white p-3.5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden flex flex-col gap-2">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $izin->status == 'Disetujui' ? 'bg-green-500' : ($izin->status == 'Ditolak' ? 'bg-red-500' : 'bg-yellow-400') }}"></div>
                
                <div class="flex justify-between items-center pl-2">
                    <span class="text-[10px] font-bold px-2 py-1 rounded-md bg-gray-100 text-gray-600">{{ $izin->jenis }}</span>
                    <span class="text-[9px] font-bold px-2 py-1 rounded-full {{ $izin->status == 'Disetujui' ? 'bg-green-100 text-green-700' : ($izin->status == 'Ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $izin->status }}
                    </span>
                </div>
                
                <div class="pl-2">
                    <p class="text-[11px] font-semibold text-gray-800 mb-0.5">
                        <i class="fa-regular fa-calendar text-[#002D8B] mr-1"></i> 
                        {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M') }} 
                        @if($izin->tanggal_mulai != $izin->tanggal_selesai) 
                            - {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }} 
                        @endif
                    </p>
                    <p class="text-[10px] text-gray-500 truncate" title="{{ $izin->alasan }}">{{ $izin->alasan }}</p>
                </div>
            </div>
        @empty
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <i class="fa-solid fa-folder-open text-gray-300 text-3xl mb-2"></i>
                <p class="text-xs text-gray-400">Belum ada riwayat izin.</p>
            </div>
        @endforelse
    </div>

    @if($riwayatIzin->hasPages())
        <div class="mt-4 flex justify-center text-xs">
            {{ $riwayatIzin->links('pagination::simple-tailwind') }}
        </div>
    @endif

</div>

@push('scripts')
<script>
    function updateClock() {
        const date = new Date();
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const separator = date.getSeconds() % 2 === 0 ? ':' : '<span class="opacity-50">:</span>';
        
        document.getElementById('realtime-clock').innerHTML = 
            `<i class="fa-regular fa-clock"></i> <span>${hours}${separator}${minutes}</span>`;
    }
    
    setInterval(updateClock, 1000);
    updateClock(); 
</script>
@endpush
@endsection