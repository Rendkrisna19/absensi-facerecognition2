@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Ringkasan Hari Ini')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="space-y-8 font-poppins pb-8">
    
    <!-- Banner Section -->
    <div class="w-full h-[300px] rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative group">
        <img 
            src="{{ asset('images/banner1.png') }}" 
            alt="Banner Dashboard" 
            class="w-full h-48 md:h-64 lg:h-80 object-cover group-hover:scale-105 transition-transform duration-1000 ease-in-out"
        >
        <!-- Opsional: Gradient Overlay jika banner terlalu terang agar tidak silau -->
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/20 to-transparent pointer-events-none"></div>
    </div>

    <!-- Welcome Section -->
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 opacity-50 blur-3xl"></div>
        
        <div class="relative z-10">
            <h3 class="text-3xl font-bold text-gray-800">Selamat Datang, <span class="text-[#1e3b8b]">{{ auth()->user()->name ?? 'Admin' }}</span>! 👋</h3>
            <p class="text-gray-500 mt-2 text-sm md:text-base">Pantau presensi dan kedisiplinan guru hari ini secara real-time dengan mudah.</p>
        </div>
        <div class="w-full md:w-auto text-left md:text-right relative z-10">
            <div class="inline-flex items-center bg-white px-5 py-3 rounded-2xl font-bold border border-gray-200 shadow-sm transition-transform hover:scale-105">
                <div class="bg-blue-100 p-2 rounded-xl mr-3">
                    <i class="fa-regular fa-calendar-check text-xl text-[#3b82f6]"></i> 
                </div>
                <span class="text-[#1e3b8b]">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:bg-gradient-to-br hover:from-blue-600 hover:to-blue-800 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-2 transition-all duration-300 ease-in-out cursor-pointer">
            <div class="absolute right-0 top-0 mt-6 mr-6 bg-blue-50 text-blue-600 p-4 rounded-2xl transition-all duration-300 group-hover:bg-white/20 group-hover:text-white group-hover:scale-110 group-hover:rotate-6">
                <i class="fa-solid fa-users text-2xl"></i>
            </div>
            <p class="text-gray-500 group-hover:text-blue-100 text-sm font-semibold uppercase tracking-wider transition-colors duration-300 mt-2">Total Guru</p>
            <h4 class="text-4xl font-bold text-gray-800 group-hover:text-white mt-3 transition-colors duration-300">
                {{ $stats['total_guru'] }} <span class="text-base font-medium text-gray-400 group-hover:text-blue-200 normal-case tracking-normal transition-colors duration-300">orang</span>
            </h4>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:bg-gradient-to-br hover:from-emerald-500 hover:to-emerald-700 hover:shadow-xl hover:shadow-emerald-500/30 hover:-translate-y-2 transition-all duration-300 ease-in-out cursor-pointer">
            <div class="absolute right-0 top-0 mt-6 mr-6 bg-green-50 text-emerald-500 p-4 rounded-2xl transition-all duration-300 group-hover:bg-white/20 group-hover:text-white group-hover:scale-110 group-hover:rotate-6">
                <i class="fa-solid fa-circle-check text-2xl"></i>
            </div>
            <p class="text-gray-500 group-hover:text-emerald-100 text-sm font-semibold uppercase tracking-wider transition-colors duration-300 mt-2">Hadir Tepat</p>
            <h4 class="text-4xl font-bold text-gray-800 group-hover:text-white mt-3 transition-colors duration-300">
                {{ $stats['hadir'] }} <span class="text-base font-medium text-gray-400 group-hover:text-emerald-200 normal-case tracking-normal transition-colors duration-300">orang</span>
            </h4>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:bg-gradient-to-br hover:from-amber-500 hover:to-orange-600 hover:shadow-xl hover:shadow-orange-500/30 hover:-translate-y-2 transition-all duration-300 ease-in-out cursor-pointer">
            <div class="absolute right-0 top-0 mt-6 mr-6 bg-orange-50 text-orange-500 p-4 rounded-2xl transition-all duration-300 group-hover:bg-white/20 group-hover:text-white group-hover:scale-110 group-hover:-rotate-6">
                <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
            </div>
            <p class="text-gray-500 group-hover:text-orange-100 text-sm font-semibold uppercase tracking-wider transition-colors duration-300 mt-2">Terlambat</p>
            <h4 class="text-4xl font-bold text-gray-800 group-hover:text-white mt-3 transition-colors duration-300">
                {{ $stats['terlambat'] }} <span class="text-base font-medium text-gray-400 group-hover:text-orange-200 normal-case tracking-normal transition-colors duration-300">orang</span>
            </h4>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:bg-gradient-to-br hover:from-rose-500 hover:to-rose-700 hover:shadow-xl hover:shadow-rose-500/30 hover:-translate-y-2 transition-all duration-300 ease-in-out cursor-pointer">
            <div class="absolute right-0 top-0 mt-6 mr-6 bg-red-50 text-rose-500 p-4 rounded-2xl transition-all duration-300 group-hover:bg-white/20 group-hover:text-white group-hover:scale-110 group-hover:rotate-6">
                <i class="fa-solid fa-circle-xmark text-2xl"></i>
            </div>
            <p class="text-gray-500 group-hover:text-rose-100 text-sm font-semibold uppercase tracking-wider transition-colors duration-300 mt-2">Belum Absen</p>
            <h4 class="text-4xl font-bold text-gray-800 group-hover:text-white mt-3 transition-colors duration-300">
                {{ $stats['alpa'] }} <span class="text-base font-medium text-gray-400 group-hover:text-rose-200 normal-case tracking-normal transition-colors duration-300">orang</span>
            </h4>
        </div>

    </div>

    <!-- QR Code Absensi Quick Access -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <!-- QR Preview -->
            <div class="flex-shrink-0">
                <div class="w-48 h-48 bg-white border-2 border-dashed border-blue-200 rounded-2xl flex items-center justify-center p-3" id="dashboard-qr"></div>
            </div>
            <!-- Info -->
            <div class="flex-1 text-center md:text-left">
                <div class="inline-flex items-center bg-blue-50 px-3 py-1 rounded-full text-xs font-bold text-blue-600 mb-3">
                    <i class="fa-solid fa-qrcode mr-1.5"></i> Akses Cepat
                </div>
                <h4 class="text-xl font-bold text-gray-800">QR Code Absensi</h4>
                <p class="text-sm text-gray-500 mt-2 max-w-md">Tampilkan halaman QR Code di layar monitor sekolah agar guru dapat melakukan scan absensi dengan mudah melalui smartphone.</p>
                <div class="mt-5 flex flex-col sm:flex-row gap-3">
                    <a href="{{ $qrAbsensiUrl }}" target="_blank" class="inline-flex items-center justify-center px-6 py-3 bg-[#002D8B] text-white font-semibold rounded-xl hover:bg-[#001f5c] transition-colors shadow-md shadow-blue-900/20">
                        <i class="fa-solid fa-up-right-from-square mr-2"></i> Buka Fullscreen
                    </a>
                    <a href="{{ route('admin.pengaturan-lan.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                        <i class="fa-solid fa-network-wired mr-2"></i> Kelola Jaringan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8" x-data="absensiModal()">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-100 pb-5 gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Aktivitas Masuk Terbaru</h4>
                <p class="text-sm text-gray-500 mt-1">Daftar absensi terakhir hari ini.</p>
            </div>
            <button @click="openModal()" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-[#1e3b8b] bg-blue-50 rounded-xl hover:bg-[#1e3b8b] hover:text-white transition-colors duration-300">
                Lihat Semua Data <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </button>
        </div>
        
        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-600 border-b border-gray-100">
                        <th class="p-5 font-semibold w-16 text-center">No</th>
                        <th class="p-5 font-semibold">Profil Guru</th>
                        <th class="p-5 font-semibold">Waktu Masuk</th>
                        <th class="p-5 font-semibold">Status Presensi</th>
                        <th class="p-5 font-semibold text-center">Metode / Jaringan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recent_absensi as $index => $absen)
                    <tr class="hover:bg-blue-50/40 transition-colors duration-200 group">
                        <td class="p-5 text-gray-500 font-medium text-center">{{ $index + 1 }}</td>
                        <td class="p-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 flex items-center justify-center font-bold text-sm shadow-inner group-hover:scale-105 transition-transform">
                                    {{ strtoupper(substr($absen->user->name ?? 'G', 0, 2)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-gray-800 text-base">{{ $absen->user->name ?? 'Guru Tidak Diketahui' }}</span>
                                    <p class="text-xs text-gray-400 font-mono mt-0.5 tracking-wide">NIK: {{ $absen->user->nik ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-5">
                            <span class="inline-flex items-center font-bold text-gray-700 bg-gray-100 px-4 py-2 rounded-xl text-xs">
                                <i class="fa-regular fa-clock text-gray-400 mr-2 text-sm"></i> 
                                {{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') }} WIB
                            </span>
                        </td>
                        <td class="p-5">
                            @if($absen->status == 'Hadir')
                                <span class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-xs font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-check-circle mr-1.5"></i> Hadir
                                </span>
                            @elseif($absen->status == 'Terlambat')
                                <span class="inline-flex items-center px-4 py-2 bg-orange-50 text-orange-600 border border-orange-100 rounded-xl text-xs font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Terlambat
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 bg-gray-50 text-gray-600 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-circle-info mr-1.5"></i> {{ $absen->status }}
                                </span>
                            @endif
                        </td>
                        <td class="p-5 text-center">
                            <div class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50/50 px-3 py-1.5 rounded-lg border border-emerald-100/50">
                                <i class="fa-solid fa-wifi"></i> Valid (LAN)
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-16 text-center bg-gray-50/30">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 mb-4">
                                    <i class="fa-regular fa-folder-open text-4xl text-gray-300"></i>
                                </div>
                                <p class="font-bold text-gray-600 text-lg">Belum Ada Presensi Masuk</p>
                                <p class="text-sm mt-1 text-gray-400">Data absensi guru untuk hari ini masih kosong.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Lihat Semua Data -->
        <div x-show="isOpen" style="display: none;" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 transition-opacity" @click="closeModal()"></div>
        <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto" x-transition>
            <div class="bg-white rounded-3xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden" @click.stop>
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Semua Data Absensi</h3>
                        <p class="text-sm text-gray-500 mt-1">Daftar kehadiran lengkap berdasarkan tanggal.</p>
                    </div>
                    <button @click="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors w-10 h-10 rounded-full hover:bg-red-50 flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="p-6 flex-1 overflow-y-auto">
                    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-3 bg-blue-50/50 p-4 rounded-2xl border border-blue-100/50">
                        <label for="filter_tanggal" class="text-sm font-semibold text-gray-700 whitespace-nowrap">Pilih Tanggal:</label>
                        <input type="date" id="filter_tanggal" x-model="filterDate" @change="fetchData()" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full sm:w-auto">
                    </div>
                    <div x-show="isLoading" class="py-12 flex justify-center items-center">
                        <i class="fa-solid fa-circle-notch fa-spin text-4xl text-[#1e3b8b]"></i>
                    </div>
                    <div x-show="!isLoading" class="overflow-x-auto rounded-2xl border border-gray-100">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 text-gray-600 border-b border-gray-100">
                                    <th class="p-4 font-semibold w-16 text-center">No</th>
                                    <th class="p-4 font-semibold">Profil Guru</th>
                                    <th class="p-4 font-semibold">Waktu Masuk</th>
                                    <th class="p-4 font-semibold">Status Presensi</th>
                                    <th class="p-4 font-semibold text-center">Metode / Jaringan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <template x-for="(absen, index) in absensiData.data" :key="absen.id">
                                    <tr class="hover:bg-blue-50/40 transition-colors duration-200">
                                        <td class="p-4 text-gray-500 font-medium text-center" x-text="(absensiData.current_page - 1) * absensiData.per_page + index + 1"></td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 flex items-center justify-center font-bold text-sm shadow-inner" x-text="absen.user && absen.user.name ? absen.user.name.substring(0, 2).toUpperCase() : 'G'"></div>
                                                <div>
                                                    <span class="font-bold text-gray-800 text-base" x-text="absen.user ? absen.user.name : 'Guru Tidak Diketahui'"></span>
                                                    <p class="text-xs text-gray-400 font-mono mt-0.5 tracking-wide" x-text="'NIK: ' + (absen.user ? (absen.user.nik || '-') : '-')"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center font-bold text-gray-700 bg-gray-100 px-3 py-1.5 rounded-xl text-xs">
                                                <i class="fa-regular fa-clock text-gray-400 mr-2 text-sm"></i> 
                                                <span x-text="formatTime(absen.jam_masuk) + ' WIB'"></span>
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <template x-if="absen.status === 'Hadir'">
                                                <span class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-xs font-bold uppercase tracking-wider">
                                                    <i class="fa-solid fa-check-circle mr-1.5"></i> Hadir
                                                </span>
                                            </template>
                                            <template x-if="absen.status === 'Terlambat'">
                                                <span class="inline-flex items-center px-3 py-1.5 bg-orange-50 text-orange-600 border border-orange-100 rounded-xl text-xs font-bold uppercase tracking-wider">
                                                    <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Terlambat
                                                </span>
                                            </template>
                                            <template x-if="absen.status !== 'Hadir' && absen.status !== 'Terlambat'">
                                                <span class="inline-flex items-center px-3 py-1.5 bg-gray-50 text-gray-600 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider">
                                                    <i class="fa-solid fa-circle-info mr-1.5"></i> <span x-text="absen.status"></span>
                                                </span>
                                            </template>
                                        </td>
                                        <td class="p-4 text-center">
                                            <div class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50/50 px-3 py-1.5 rounded-lg border border-emerald-100/50">
                                                <i class="fa-solid fa-wifi"></i> Valid (LAN)
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="absensiData.data && absensiData.data.length === 0">
                                    <tr>
                                        <td colspan="5" class="p-12 text-center bg-gray-50/30">
                                            <div class="flex flex-col items-center justify-center text-gray-400">
                                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 mb-3">
                                                    <i class="fa-regular fa-folder-open text-3xl text-gray-300"></i>
                                                </div>
                                                <p class="font-bold text-gray-600">Tidak ada data absensi</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div x-show="absensiData.last_page > 1" class="mt-6 flex flex-col sm:flex-row items-center justify-between border-t border-gray-100 pt-4 gap-4">
                        <p class="text-sm text-gray-500">
                            Menampilkan <span class="font-bold text-gray-800" x-text="absensiData.from || 0"></span> - <span class="font-bold text-gray-800" x-text="absensiData.to || 0"></span> dari <span class="font-bold text-gray-800" x-text="absensiData.total || 0"></span> data
                        </p>
                        <div class="flex gap-2">
                            <button @click="fetchData(absensiData.prev_page_url)" :disabled="!absensiData.prev_page_url" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <i class="fa-solid fa-angle-left mr-1"></i> Prev
                            </button>
                            <button @click="fetchData(absensiData.next_page_url)" :disabled="!absensiData.next_page_url" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                Next <i class="fa-solid fa-angle-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
// Generate QR Code di dashboard
(function() {
    const qrContainer = document.getElementById('dashboard-qr');
    if (qrContainer && typeof QRCode !== 'undefined') {
        qrContainer.innerHTML = '';
        new QRCode(qrContainer, {
            text: '{{ $qrAbsensiUrl }}',
            width: 160,
            height: 160,
            colorDark: '#002D8B',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }
})();
</script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('absensiModal', () => ({
        isOpen: false,
        isLoading: false,
        filterDate: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}',
        absensiData: {},
        
        openModal() {
            this.isOpen = true;
            if (!this.absensiData.data) {
                this.fetchData();
            }
        },
        
        closeModal() {
            this.isOpen = false;
        },
        
        fetchData(url = null) {
            this.isLoading = true;
            let fetchUrl = url || `{{ route('admin.dashboard.semua-absen') }}?tanggal=${this.filterDate}`;
            
            if (url && !url.includes('tanggal=')) {
                fetchUrl += (fetchUrl.includes('?') ? '&' : '?') + `tanggal=${this.filterDate}`;
            }

            fetch(fetchUrl)
                .then(res => res.json())
                .then(data => {
                    this.absensiData = data;
                    this.isLoading = false;
                })
                .catch(err => {
                    console.error('Error fetching data:', err);
                    this.isLoading = false;
                });
        },
        
        formatTime(datetimeStr) {
            if (!datetimeStr) return '-';
            return datetimeStr.substring(11, 16);
        }
    }));
});
</script>
@endsection