@extends('layouts.app')

@section('title', 'Pengaturan Absensi')
@section('page_title', 'Jam Operasional & Denda')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body, .font-poppins { font-family: 'Poppins', sans-serif !important; }
    
    /* Input Time Customization */
    input[type="time"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.6;
        transition: 0.2s;
    }
    input[type="time"]::-webkit-calendar-picker-indicator:hover {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="font-poppins pb-8 space-y-6">
    
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold flex items-center shadow-sm animate-fade-in-down">
            <i class="fa-solid fa-circle-check text-xl mr-3 text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden">
        
        <!-- Decorative Background Blob -->
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 opacity-50 blur-3xl z-0 pointer-events-none"></div>

        <!-- Header Card -->
        <div class="bg-white/50 backdrop-blur-sm px-8 py-8 border-b border-gray-100 flex flex-col md:flex-row md:items-center gap-6 relative z-10">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 text-[#1e3b8b] flex items-center justify-center text-3xl shadow-inner shrink-0 border border-blue-100/50">
                <i class="fa-solid fa-business-time"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Aturan Jam Kerja & Potongan Gaji</h3>
                <p class="text-sm text-gray-500 mt-2 font-medium">Sistem Face Recognition akan menyesuaikan waktu buka/tutup kamera dan kalkulasi denda otomatis berdasarkan jadwal yang Anda atur di bawah ini.</p>
            </div>
        </div>

        <!-- Form Body -->
        <form action="{{ route('admin.pengaturan-absensi.store') }}" method="POST" class="p-8 relative z-10">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- KIRI: Pengaturan Waktu (Mengambil 2 kolom di layar besar) -->
                <div class="lg:col-span-2 space-y-6">
                    <h4 class="font-bold text-gray-800 text-lg flex items-center gap-3 pb-3 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-[#1e3b8b] text-sm">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        Pengaturan Waktu
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Jam Buka Absen -->
                        <div class="bg-emerald-50/30 p-5 rounded-2xl border border-emerald-100 hover:border-emerald-300 transition-all duration-300 group hover:shadow-md hover:shadow-emerald-100/50 relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 text-emerald-50 opacity-50 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-door-open text-6xl"></i>
                            </div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 relative z-10">Scanner Masuk Dibuka</label>
                            <div class="relative z-10">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-emerald-500">
                                    <i class="fa-solid fa-door-open"></i>
                                </div>
                                <input type="time" name="jam_buka_absen" value="{{ \Carbon\Carbon::parse($pengaturan->jam_buka_absen)->format('H:i') }}" required class="w-full pl-11 pr-4 py-3 bg-white border-none ring-1 ring-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono font-bold text-gray-700 shadow-sm transition-shadow">
                            </div>
                            <p class="text-[11px] text-gray-500 font-medium mt-3 relative z-10"><i class="fa-solid fa-circle-info mr-1.5 text-emerald-400"></i>Guru baru bisa mulai absen pada jam ini.</p>
                        </div>

                        <!-- Batas Jam Masuk (Telat) -->
                        <div class="bg-rose-50/30 p-5 rounded-2xl border border-rose-100 hover:border-rose-300 transition-all duration-300 group hover:shadow-md hover:shadow-rose-100/50 relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 text-rose-50 opacity-50 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-user-clock text-6xl"></i>
                            </div>
                            <label class="block text-sm font-bold text-rose-700 mb-3 relative z-10">Batas Keterlambatan</label>
                            <div class="relative z-10">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-rose-500">
                                    <i class="fa-solid fa-user-clock"></i>
                                </div>
                                <input type="time" name="batas_jam_masuk" value="{{ \Carbon\Carbon::parse($pengaturan->batas_jam_masuk)->format('H:i') }}" required class="w-full pl-11 pr-4 py-3 bg-white border-none ring-1 ring-rose-200 text-rose-700 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none font-mono font-bold shadow-sm transition-shadow">
                            </div>
                            <p class="text-[11px] text-rose-500 font-medium mt-3 relative z-10"><i class="fa-solid fa-triangle-exclamation mr-1.5"></i>Melewati batas ini status menjadi "Terlambat".</p>
                        </div>

                        <!-- Jam Pulang -->
                        <div class="bg-blue-50/30 p-5 rounded-2xl border border-blue-100 hover:border-blue-300 transition-all duration-300 group hover:shadow-md hover:shadow-blue-100/50 relative overflow-hidden md:col-span-2">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-blue-50 opacity-50 group-hover:scale-110 transition-transform duration-300 pointer-events-none hidden sm:block">
                                <i class="fa-solid fa-person-walking-arrow-right text-6xl"></i>
                            </div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 relative z-10">Jam Boleh Pulang</label>
                            <div class="relative w-full md:w-1/2 z-10">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-blue-500">
                                    <i class="fa-solid fa-person-walking-arrow-right"></i>
                                </div>
                                <input type="time" name="jam_pulang" value="{{ \Carbon\Carbon::parse($pengaturan->jam_pulang)->format('H:i') }}" required class="w-full pl-11 pr-4 py-3 bg-white border-none ring-1 ring-blue-200 rounded-xl focus:ring-2 focus:ring-[#1e3b8b] focus:outline-none font-mono font-bold text-gray-700 shadow-sm transition-shadow">
                            </div>
                            <p class="text-[11px] text-gray-500 font-medium mt-3 relative z-10"><i class="fa-solid fa-circle-info mr-1.5 text-blue-400"></i>Scanner absen pulang baru akan dibuka mulai jam ini.</p>
                        </div>
                    </div>
                </div>

                <!-- KANAN: Pengaturan Denda (Mengambil 1 kolom di layar besar) -->
                <div class="space-y-6">
                    <h4 class="font-bold text-gray-800 text-lg flex items-center gap-3 pb-3 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center text-amber-500 text-sm">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        Pengaturan Denda
                    </h4>
                    
                    <div class="bg-gradient-to-b from-amber-50 to-orange-50/30 p-6 rounded-2xl border border-amber-100 h-full flex flex-col justify-start relative overflow-hidden shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300">
                        <!-- BG Accent -->
                        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-amber-200/20 rounded-full blur-2xl"></div>

                        <label class="block text-sm font-bold text-gray-800 mb-4 relative z-10">Nominal Denda Terlambat</label>
                        
                        <div class="relative mb-5 z-10">
                            <div class="absolute inset-y-0 left-0 flex items-center justify-center w-14 pointer-events-none font-bold text-amber-700 bg-amber-100 border-r border-amber-200 rounded-l-xl h-full">
                                Rp
                            </div>
                            <input type="number" name="denda_terlambat" value="{{ $pengaturan->denda_terlambat }}" required class="w-full pl-16 pr-4 py-3.5 text-xl font-extrabold bg-white border-none ring-1 ring-amber-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none shadow-sm text-gray-800 tracking-wide transition-shadow">
                        </div>
                        
                        <div class="flex items-start gap-3 mt-auto bg-white/70 p-4 rounded-xl border border-amber-200/60 shadow-sm relative z-10">
                            <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-info text-xs"></i>
                            </div>
                            <p class="text-xs text-gray-600 leading-relaxed font-medium">
                                Sistem memberlakukan <span class="font-bold text-gray-800">Denda Flat</span>. Jika guru absen melewati batas jam masuk, nominal ini akan dipotong secara otomatis pada rekapitulasi gaji.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Area Tombol -->
            <div class="flex justify-end pt-8 mt-8 border-t border-gray-100">
                <button type="submit" class="bg-[#1e3b8b] hover:bg-blue-800 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg shadow-blue-900/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-900/30 flex items-center gap-2 group">
                    <i class="fa-solid fa-floppy-disk group-hover:scale-110 transition-transform"></i> Simpan Pengaturan
                </button>
            </div>
            
        </form>
    </div>
</div>
@endsection