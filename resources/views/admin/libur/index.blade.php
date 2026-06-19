@extends('layouts.app')

@section('title', 'Pengaturan Libur Semester')
@section('page_title', 'Manajemen Libur Semester')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body, .font-poppins { font-family: 'Poppins', sans-serif !important; }
    [x-cloak] { display: none !important; }
    
    /* Custom Scrollbar */
    .table-scroll::-webkit-scrollbar { height: 8px; }
    .table-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 8px; }
    .table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
    .table-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endpush

@section('content')
<div class="font-poppins pb-8 space-y-6" x-data="{ openAdd: false }">
    
    <!-- Header Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 opacity-50 blur-3xl z-0"></div>
        
        <div class="flex items-center gap-5 relative z-10">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-2xl text-blue-600 shadow-inner">
                <i class="fa-solid fa-umbrella-beach text-3xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Kalender Libur Semester</h2>
                <p class="text-sm text-gray-500 mt-1">Tentukan rentang tanggal libur agar sistem presensi dinonaktifkan otomatis.</p>
            </div>
        </div>
        
        <div class="relative z-10 w-full md:w-auto">
            <button @click="openAdd = true" class="w-full md:w-auto inline-flex items-center justify-center bg-[#24429b] hover:bg-blue-800 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-lg shadow-blue-900/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-900/30 gap-2">
                <i class="fa-solid fa-calendar-plus bg-white/20 p-1.5 rounded-lg text-xs"></i> Tambah Libur
            </button>
        </div>
    </div>

    <!-- Main Content Box -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <div class="overflow-x-auto rounded-2xl border border-gray-100 table-scroll">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-[#24429b] text-white">
                    <tr>
                        <th class="px-6 py-4 font-semibold rounded-tl-2xl">Nama Semester / Keterangan</th>
                        <th class="px-6 py-4 font-semibold w-40">Mulai</th>
                        <th class="px-6 py-4 font-semibold w-40">Selesai</th>
                        <th class="px-6 py-4 font-semibold text-center w-48">Status</th>
                        <th class="px-6 py-4 font-semibold text-center w-24 rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse($liburs as $l)
                    <tr class="hover:bg-blue-50/40 transition-colors duration-200 group">
                        <td class="px-6 py-5">
                            <div class="flex items-start gap-3">
                                <div class="mt-1 w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs border border-blue-100">
                                    <i class="fa-solid fa-tag"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-base">{{ $l->nama_semester }}</p>
                                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                                        <i class="fa-regular fa-comment-dots text-gray-400"></i>
                                        {{ $l->keterangan ?? 'Tidak ada catatan tambahan' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 font-semibold text-gray-700 text-xs">
                                <i class="fa-regular fa-calendar text-gray-400"></i>
                                {{ \Carbon\Carbon::parse($l->tanggal_mulai)->translatedFormat('d M Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 font-semibold text-gray-700 text-xs">
                                <i class="fa-regular fa-calendar-check text-gray-400"></i>
                                {{ \Carbon\Carbon::parse($l->tanggal_selesai)->translatedFormat('d M Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if(now()->between($l->tanggal_mulai, $l->tanggal_selesai))
                                <span class="inline-flex items-center bg-emerald-50 text-emerald-600 border border-emerald-100 px-3 py-1.5 rounded-xl text-[11px] font-bold uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span> Sedang Libur
                                </span>
                            @elseif(now()->lt($l->tanggal_mulai))
                                <span class="inline-flex items-center bg-amber-50 text-amber-600 border border-amber-100 px-3 py-1.5 rounded-xl text-[11px] font-bold uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span> Akan Datang
                                </span>
                            @else
                                <span class="inline-flex items-center bg-gray-50 text-gray-500 border border-gray-200 px-3 py-1.5 rounded-xl text-[11px] font-bold uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span> Selesai
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center">
                            <form action="{{ route('admin.libur.destroy', $l->id) }}" method="POST" class="inline-block">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-white text-rose-500 hover:bg-rose-500 hover:text-white w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-300 shadow-sm border border-rose-100 hover:border-transparent hover:-translate-y-1 hover:shadow-md opacity-80 group-hover:opacity-100" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal libur ini?')" title="Hapus Jadwal">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-16 text-center bg-gray-50/30">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 mb-4">
                                    <i class="fa-solid fa-mug-hot text-4xl text-gray-300"></i>
                                </div>
                                <p class="font-bold text-gray-600 text-lg">Belum Ada Data Libur</p>
                                <p class="text-sm mt-1 text-gray-400">Klik tombol "Tambah Libur" untuk mulai menjadwalkan libur semester.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form (Alpine.js) -->
    <div x-show="openAdd" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
         
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden"
             @click.away="openAdd = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95">
            
            <div class="bg-[#24429b] p-6 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Tambah Rentang Libur</h3>
                    <p class="text-blue-100 text-xs mt-1">Isi form di bawah untuk menjadwalkan libur baru</p>
                </div>
                <button @click="openAdd = false" class="text-blue-200 hover:text-white transition-colors bg-white/10 w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.libur.store') }}" method="POST" class="p-8">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Nama Semester <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-pen-clip"></i>
                            </div>
                            <input type="text" name="nama_semester" required class="w-full border-none ring-1 ring-gray-200 bg-gray-50 rounded-xl pl-10 p-3 text-sm focus:ring-2 focus:ring-[#24429b] outline-none transition-all" placeholder="Cth: Semester Genap 2026/2027">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Tanggal Mulai <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_mulai" required class="w-full border-none ring-1 ring-gray-200 bg-gray-50 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#24429b] outline-none transition-all text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Tanggal Selesai <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_selesai" required class="w-full border-none ring-1 ring-gray-200 bg-gray-50 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#24429b] outline-none transition-all text-gray-700">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Keterangan (Opsional)</label>
                        <textarea name="keterangan" rows="3" class="w-full border-none ring-1 ring-gray-200 bg-gray-50 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#24429b] outline-none transition-all resize-none placeholder-gray-400" placeholder="Tambahkan catatan khusus jika diperlukan..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                    <button type="button" @click="openAdd = false" class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Batal</button>
                    <button type="submit" class="bg-[#24429b] hover:bg-blue-800 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-900/20 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection