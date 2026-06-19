@extends('layouts.mobile')

@section('title', 'Izin & Cuti')
@section('page_title', 'Riwayat Izin')
@section('subtitle', 'Daftar Pengajuan')

@section('content')
    <div class="mb-6">
        <a href="{{ route('guru.pengajuan-izin.create') }}" class="w-full flex items-center justify-center gap-2 bg-[#002D8B] text-white py-3.5 rounded-xl font-semibold shadow-md active:scale-95 transition-transform">
            <i class="fa-solid fa-plus"></i> Ajukan Izin / Sakit Baru
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium">
            <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($pengajuanIzins as $izin)
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 
                    {{ $izin->status == 'Disetujui' ? 'bg-green-500' : ($izin->status == 'Ditolak' ? 'bg-red-500' : 'bg-yellow-400') }}">
                </div>
                
                <div class="flex justify-between items-start mb-2 pl-2">
                    <div>
                        <span class="text-xs font-bold px-2 py-1 rounded-md bg-gray-100 text-gray-600">{{ $izin->jenis }}</span>
                        <p class="text-[11px] text-gray-400 mt-1">Diajukan: {{ $izin->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full 
                        {{ $izin->status == 'Disetujui' ? 'bg-green-100 text-green-700' : ($izin->status == 'Ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $izin->status }}
                    </span>
                </div>

                <div class="pl-2 mt-3">
                    <div class="flex items-center text-sm text-gray-800 font-semibold mb-1">
                        <i class="fa-regular fa-calendar text-[#002D8B] mr-2"></i>
                        {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }}
                        @if($izin->tanggal_mulai != $izin->tanggal_selesai)
                            - {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 line-clamp-2 italic">"{{ $izin->alasan }}"</p>
                </div>
                
                @if($izin->status == 'Ditolak' && $izin->catatan_penolakan)
                    <div class="pl-2 mt-2 bg-red-50 p-2 rounded-lg border border-red-100">
                        <p class="text-[10px] text-red-600 font-medium">Alasan ditolak: {{ $izin->catatan_penolakan }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-10">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                    <i class="fa-solid fa-folder-open text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">Belum ada riwayat pengajuan izin.</p>
            </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $pengajuanIzins->links() }}
    </div>
@endsection