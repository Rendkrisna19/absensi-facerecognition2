@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page_title', 'Riwayat Absensi Keseluruhan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium flex items-center">
            <i class="fa-solid fa-circle-xmark mr-2"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Log Kehadiran Guru</h3>
            <p class="text-sm text-gray-500 mt-1">Pantau seluruh riwayat jam masuk, jam pulang, dan status izin guru.</p>
        </div>
        <div class="flex flex-wrap gap-2" x-data="{ openCleanup: false }">
            <button @click="openCleanup = true" type="button" class="bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200 px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-broom"></i> Bersihkan Data
            </button>
            <a href="{{ route('admin.riwayat-absensi.pdf', request()->all()) }}" target="_blank" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 hover:border-transparent px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.riwayat-absensi.excel', request()->all()) }}" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border border-emerald-200 hover:border-transparent px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-file-excel"></i> Excel
            </a>

            <!-- Modal Cleanup Data Absensi -->
            <template x-teleport="body">
                <div x-show="openCleanup" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                    <div x-show="openCleanup" x-transition.opacity @click="openCleanup = false" class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm"></div>
                    <div x-show="openCleanup" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden z-10">
                        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold flex items-center gap-2 text-gray-800"><i class="fa-solid fa-broom text-[#002D8B]"></i> Bersihkan Data Absensi</h3>
                            <button @click="openCleanup = false" class="text-gray-400 hover:text-gray-600 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        <form id="form-cleanup-absensi" action="{{ route('admin.riwayat-absensi.cleanup') }}" method="POST" class="p-5" x-data="{ tipeHapus: 'hari' }">
                            @csrf
                            <div class="bg-red-50 border border-red-200 text-red-600 text-xs px-3 py-2 rounded-lg mb-4 flex gap-2">
                                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                                <span>Tindakan ini akan menghapus data absensi secara permanen.</span>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">Mode Pembersihan</label>
                                <select name="tipe_hapus" x-model="tipeHapus" class="w-full border-2 border-gray-300 rounded-xl text-sm focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5 transition bg-gray-50 hover:bg-white cursor-pointer font-medium">
                                    <option value="hari">Hapus 1 Hari Tertentu</option>
                                    <option value="minggu">Hapus 1 Minggu Tertentu</option>
                                    <option value="semua">Hapus Semua Data Absensi</option>
                                </select>
                            </div>
                            
                            <div class="mb-5" x-show="tipeHapus == 'hari'">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">Pilih Tanggal</label>
                                <input type="date" name="tanggal" required="required" :disabled="tipeHapus !== 'hari'" class="w-full border-2 border-gray-300 rounded-xl text-sm focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5 transition bg-white shadow-sm font-medium text-gray-700">
                            </div>

                            <div class="mb-5" x-show="tipeHapus == 'minggu'" style="display: none;">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">Pilih Minggu (Week)</label>
                                <input type="week" name="minggu" required="required" :disabled="tipeHapus !== 'minggu'" class="w-full border-2 border-gray-300 rounded-xl text-sm focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5 transition bg-white shadow-sm font-medium text-gray-700">
                                <p class="text-[10px] text-gray-400 mt-1.5">*Pilih minggu ke berapa dalam suatu tahun</p>
                            </div>

                            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                                <button type="button" @click="openCleanup = false" class="px-4 py-2.5 bg-gray-100 text-gray-700 border border-gray-200 rounded-xl text-sm font-bold hover:bg-gray-200 transition">Batal</button>
                                <button type="button" onclick="confirmCleanupAbsensi()" class="px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 transition-colors shadow-sm flex items-center gap-1.5">
                                    <i class="fa-solid fa-trash-can"></i> Hapus Permanen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.riwayat-absensi.index') }}" class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cari Guru</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-white border border-gray-300 text-sm rounded-lg focus:ring-[#002D8B] focus:border-[#002D8B] pl-10 p-2.5" placeholder="Nama atau NIK...">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Kehadiran</label>
            <select name="status" class="w-full bg-white border border-gray-300 text-sm rounded-lg focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5">
                <option value="all">Semua Status</option>
                <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="Alpa" {{ request('status') == 'Alpa' ? 'selected' : '' }}>Alpa (Tanpa Keterangan)</option>
                <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Unit Sekolah</label>
            <select name="unit_sekolah" class="w-full bg-white border border-gray-300 text-sm rounded-lg focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5">
                <option value="all">Semua Unit</option>
                <option value="SD" {{ request('unit_sekolah') == 'SD' ? 'selected' : '' }}>SD</option>
                <option value="SMP" {{ request('unit_sekolah') == 'SMP' ? 'selected' : '' }}>SMP</option>
            </select>
        </div>

        <div class="flex gap-2">
            <div class="w-1/2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bulan</label>
                <select name="bulan" class="w-full bg-white border border-gray-300 text-sm rounded-lg focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5">
                    <option value="all">Semua</option>
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-1/2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tahun</label>
                <select name="tahun" class="w-full bg-white border border-gray-300 text-sm rounded-lg focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5">
                    <option value="all">Semua</option>
                    @for($y=date('Y'); $y>=date('Y')-3; $y--)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="w-full bg-[#002D8B] hover:bg-[#001f63] text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm h-[42px] flex items-center justify-center">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('admin.riwayat-absensi.index') }}" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-semibold rounded-lg transition-all flex items-center justify-center h-[42px]" title="Reset Filter">
                <i class="fa-solid fa-rotate-right"></i>
            </a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-[#002D8B] text-white">
                <tr>
                    <th class="p-4 font-semibold w-16">No</th>
                    <th class="p-4 font-semibold">Pegawai</th>
                    <th class="p-4 font-semibold text-center">Tanggal</th>
                    <th class="p-4 font-semibold text-center">Jam Masuk</th>
                    <th class="p-4 font-semibold text-center">Jam Pulang</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($riwayat as $index => $absen)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="p-4 text-gray-600">{{ $riwayat->firstItem() + $index }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs border border-blue-200 shrink-0">
                                {{ strtoupper(substr($absen->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-800">{{ $absen->user->name ?? 'User Terhapus' }}</div>
                                <div class="text-[11px] text-gray-500 font-mono tracking-wide">
                                    {{ $absen->user->nik ?? '-' }} 
                                    @if($absen->user)
                                     • <span class="{{ $absen->user->unit_sekolah == 'SD' ? 'text-red-500' : 'text-blue-500' }} font-bold">Unit {{ $absen->user->unit_sekolah ?? 'Umum' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-center font-medium text-gray-700">
                        {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}
                    </td>
                    <td class="p-4 text-center font-mono">
                        @if($absen->jam_masuk)
                            <span class="bg-gray-100 px-2 py-1 rounded text-gray-800">{{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') }}</span>
                            @if($absen->menit_terlambat > 0)
                                @php
                                    $jam = floor($absen->menit_terlambat / 60);
                                    $menit = $absen->menit_terlambat % 60;
                                    $teksTelat = '';
                                    if($jam > 0) $teksTelat .= $jam . ' jam ';
                                    if($menit > 0) $teksTelat .= $menit . ' mnt';
                                @endphp
                                <div class="text-[10px] text-red-500 mt-1 font-sans font-semibold">Telat {{ trim($teksTelat) }}</div>
                            @endif
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-center font-mono">
                        @if($absen->jam_pulang)
                            <span class="bg-gray-100 px-2 py-1 rounded text-gray-800">{{ \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        @php
                            $badgeClass = 'bg-gray-100 text-gray-600';
                            if($absen->status == 'Hadir') $badgeClass = 'bg-green-100 text-green-700 border-green-200';
                            if($absen->status == 'Terlambat') $badgeClass = 'bg-orange-100 text-orange-700 border-orange-200';
                            if($absen->status == 'Alpa') $badgeClass = 'bg-red-100 text-red-700 border-red-200';
                            if(in_array($absen->status, ['Sakit', 'Izin', 'Cuti'])) $badgeClass = 'bg-blue-100 text-blue-700 border-blue-200';
                        @endphp
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full border {{ $badgeClass }}">
                            {{ $absen->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-12 text-gray-500">
                        <i class="fa-solid fa-calendar-xmark text-4xl mb-3 text-gray-300"></i>
                        <p class="font-bold text-gray-600">Tidak ada riwayat absensi ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
        <div class="text-sm text-gray-500 font-medium">
            Menampilkan <span class="font-bold text-[#002D8B] bg-blue-50 px-2 py-0.5 rounded-md">{{ $riwayat->firstItem() ?? 0 }}</span> 
            sampai <span class="font-bold text-[#002D8B] bg-blue-50 px-2 py-0.5 rounded-md">{{ $riwayat->lastItem() ?? 0 }}</span> 
            dari total <span class="font-bold text-[#002D8B] bg-blue-50 px-2 py-0.5 rounded-md">{{ $riwayat->total() }}</span> data.
        </div>
        <div class="pagination-wrapper">
            {{ $riwayat->links() }}
        </div>
    </div>
</div>

<script>
    function confirmCleanupAbsensi() {
        if(typeof Swal === 'undefined') {
            if(confirm('Apakah Anda yakin ingin menghapus data ini secara permanen?')) {
                document.getElementById('form-cleanup-absensi').submit();
            }
            return;
        }
        Swal.fire({
            title: 'Bersihkan Data Absensi?',
            text: "Data yang dipilih akan dihapus secara permanen dan tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', 
            cancelButtonColor: '#6b7280', 
            confirmButtonText: '<i class="fa-solid fa-trash-can mr-1"></i> Ya, Hapus Permanen!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-cleanup-absensi').submit();
            }
        });
    }
</script>
@endsection