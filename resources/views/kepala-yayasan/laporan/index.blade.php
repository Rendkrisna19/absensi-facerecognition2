@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('page_title', 'Laporan Kehadiran Guru')

@section('content')
<div class="space-y-6">

    <!-- Card Filter Pencarian (Server-Side Filter) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                    <i class="fa-solid fa-filter"></i>
                </div>
                Filter Laporan (Pilih Rentang Waktu)
            </h4>
        </div>
        
        <div class="p-6">
            <form action="{{ route('yayasan.laporan.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-5 items-end">
                    
                    <!-- Filter Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Dari Tanggal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-regular fa-calendar"></i>
                            </div>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>
                    
                    <!-- Filter Tanggal Akhir -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sampai Tanggal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-regular fa-calendar-check"></i>
                            </div>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>

                    <!-- Filter Unit Sekolah -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Unit Sekolah</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <select name="unit_sekolah" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm appearance-none">
                                <option value="Semua" {{ request('unit_sekolah') == 'Semua' ? 'selected' : '' }}>Semua Unit</option>
                                <option value="SD" {{ request('unit_sekolah') == 'SD' ? 'selected' : '' }}>SD</option>
                                <option value="SMP" {{ request('unit_sekolah') == 'SMP' ? 'selected' : '' }}>SMP</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Nama Guru -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Guru</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <select name="guru_id" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm appearance-none">
                                <option value="">-- Semua Guru --</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ request('guru_id') == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex gap-2 h-[42px]">
                        <button type="submit" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-sm">
                            <i class="fa-solid fa-magnifying-glass"></i> Filter
                        </button>
                        <a href="{{ route('yayasan.laporan.index') }}" class="px-4 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-semibold rounded-xl transition-all flex items-center justify-center" title="Reset Filter">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- CARD REKAP ABSENSI -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Absen -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-blue-500">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl"><i class="fa-solid fa-clipboard-user"></i></div>
            <div>
                <p class="text-sm text-gray-500 font-semibold">Total Data</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $summary['total'] }}</h4>
            </div>
        </div>
        <!-- Total Hadir -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-green-500">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-xl"><i class="fa-solid fa-check-circle"></i></div>
            <div>
                <p class="text-sm text-gray-500 font-semibold">Tepat Waktu (Hadir)</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $summary['hadir'] }}</h4>
            </div>
        </div>
        <!-- Total Terlambat -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-orange-500">
            <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center text-xl"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div>
                <p class="text-sm text-gray-500 font-semibold">Terlambat</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $summary['terlambat'] }}</h4>
            </div>
        </div>
        <!-- Total Alpa -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-red-500">
            <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-xl"><i class="fa-solid fa-circle-xmark"></i></div>
            <div>
                <p class="text-sm text-gray-500 font-semibold">Alpa / Tidak Hadir</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $summary['alpa'] }}</h4>
            </div>
        </div>
    </div>

    <!-- Card Tabel Data (Client-Side Pagination & Search) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
        
        <!-- Controls Pagination & Search (Tanpa Reload) -->
        <div class="flex flex-col md:flex-row justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100 mb-4 gap-4">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm text-gray-600 font-medium">Tampilkan</span>
                <select id="perPage" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-20">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-600 font-medium">Data</span>
            </div>

            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <!-- Search Bar -->
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </div>
                    <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Cari nama guru atau status...">
                </div>
                
                <!-- Export Buttons -->
              <div class="flex gap-2">
    <a href="{{ route('yayasan.laporan.excel', request()->all()) }}" class="bg-green-50 text-green-700 border border-green-200 px-3 py-2.5 rounded-lg hover:bg-green-600 hover:text-white transition flex items-center justify-center" title="Export Excel">
        <i class="fa-solid fa-file-excel"></i>
    </a>
    <a href="{{ route('yayasan.laporan.pdf', request()->all()) }}" target="_blank" class="bg-red-50 text-red-700 border border-red-200 px-3 py-2.5 rounded-lg hover:bg-red-600 hover:text-white transition flex items-center justify-center" title="Cetak PDF">
        <i class="fa-solid fa-file-pdf"></i>
    </a>
</div>
            </div>
        </div>

        <!-- TABEL UTAMA -->
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-left border-collapse" id="dataTable">
                <thead>
                    <tr class="bg-blue-800 text-white text-sm uppercase tracking-wider border-b border-blue-900">
                        <th class="px-6 py-4 font-semibold">Tanggal & Hari</th>
                        <th class="px-6 py-4 font-semibold">Profil Guru</th>
                        <th class="px-6 py-4 font-semibold text-center">Jam Masuk</th>
                        <th class="px-6 py-4 font-semibold text-center">Keterlambatan</th>
                        <th class="px-6 py-4 font-semibold text-center">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="text-sm bg-white" id="tableBody">
                    @forelse($absensis as $absen)
                    <!-- data-search dipasang agar JS gampang memfilter teks -->
                    <tr class="data-row border-b border-gray-50 hover:bg-blue-50/50 transition-colors group"
                        data-search="{{ strtolower($absen->user->name ?? '') }} {{ strtolower($absen->status) }} {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}">
                        
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d F Y') }}</div>
                            <div class="text-xs text-gray-500 mt-0.5"><i class="fa-regular fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l') }}</div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $nameParts = explode(' ', $absen->user->name ?? 'U T');
                                    $initials = substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '');
                                @endphp
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm border border-blue-200 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    {{ strtoupper($initials) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800">{{ $absen->user->name ?? 'User Terhapus' }}</div>
                                    <div class="text-xs text-gray-500">{{ $absen->user->jabatan ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <span class="font-mono bg-gray-100 text-gray-800 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold shadow-sm">
                                <i class="fa-regular fa-clock mr-1 text-gray-500"></i>
                                {{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '--:--' }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            @if($absen->menit_terlambat > 0)
                                <span class="inline-flex items-center justify-center bg-red-50 text-red-600 border border-red-100 px-3 py-1 rounded-lg font-bold text-xs">
                                    +{{ $absen->menit_terlambat }} Menit
                                </span>
                            @else
                                <span class="text-gray-400 font-bold">-</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            @if($absen->status == 'Hadir')
                                <span class="inline-flex items-center px-3 py-1 bg-green-50 border border-green-200 text-green-700 rounded-full text-xs font-bold shadow-sm">
                                    <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> Tepat Waktu
                                </span>
                            @elseif($absen->status == 'Terlambat')
                                <span class="inline-flex items-center px-3 py-1 bg-orange-50 border border-orange-200 text-orange-700 rounded-full text-xs font-bold shadow-sm">
                                    <span class="w-2 h-2 rounded-full bg-orange-500 mr-2"></span> Terlambat
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 bg-red-50 border border-red-200 text-red-700 rounded-full text-xs font-bold shadow-sm">
                                    <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Alpa
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyStateFallback" class="border-b border-gray-50">
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                <i class="fa-regular fa-folder-open text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-1">Data Tidak Ditemukan</h3>
                            <p class="text-sm text-gray-500">Tidak ada riwayat absensi untuk kriteria filter yang dipilih.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- UI Pagination (Tanpa Reload via JS) -->
        @if($absensis->count() > 0)
        <div class="flex flex-col md:flex-row justify-between items-center mt-6 gap-4" id="paginationControls">
            <div class="text-sm text-gray-600 font-medium">
                Menampilkan <span id="infoStart" class="font-bold text-gray-900">0</span> sampai <span id="infoEnd" class="font-bold text-gray-900">0</span> dari <span id="infoTotal" class="font-bold text-gray-900">0</span> entri
            </div>
            <div class="inline-flex rounded-md shadow-sm" id="paginationButtons">
                <!-- Tombol ini digenerate otomatis oleh Javascript -->
            </div>
        </div>
        @endif
    </div>
</div>

<!-- ==============================================
     SCRIPT PAGINATION & SEARCH TANPA RELOAD
     ============================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = Array.from(document.querySelectorAll('.data-row'));
        
        // Jika tidak ada data sama sekali dari server, hentikan eksekusi script
        if(rows.length === 0) return;

        const tableBody = document.getElementById('tableBody');
        const searchInput = document.getElementById('searchInput');
        const perPageSelect = document.getElementById('perPage');
        
        let currentPage = 1;
        let perPage = parseInt(perPageSelect.value);
        let filteredRows = [...rows]; 

        // Fungsi Master Update Tabel
        function updateTable() {
            const searchTerm = searchInput.value.toLowerCase();

            // 1. Lakukan Filter / Search Teks
            filteredRows = rows.filter(row => {
                const textData = row.getAttribute('data-search');
                return textData.includes(searchTerm);
            });

            // 2. Kalkulasi Pagination
            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / perPage);
            if (currentPage > totalPages) currentPage = totalPages || 1;

            const startIdx = (currentPage - 1) * perPage;
            const endIdx = startIdx + perPage;

            // 3. Sembunyikan Semua
            rows.forEach(row => row.style.display = 'none');

            // 4. Munculkan hanya baris yang lolos filter & sesuai pagination
            filteredRows.slice(startIdx, endIdx).forEach(row => {
                row.style.display = ''; 
            });

            // 5. Update Label Teks di Bawah Tabel
            document.getElementById('infoStart').innerText = totalRows === 0 ? 0 : startIdx + 1;
            document.getElementById('infoEnd').innerText = Math.min(endIdx, totalRows);
            document.getElementById('infoTotal').innerText = totalRows;

            // 6. Buat Tombol Angka Pagination
            generatePagination(totalPages);
        }

        // Fungsi Membangun Tombol
        function generatePagination(totalPages) {
            const container = document.getElementById('paginationButtons');
            container.innerHTML = '';

            if (totalPages <= 1) return; // Jika cuma 1 halaman, hilangkan tombolnya

            // Tombol Previous
            const btnPrev = document.createElement('button');
            btnPrev.innerHTML = '<i class="fa-solid fa-chevron-left text-xs"></i>';
            btnPrev.className = `px-3 py-2 text-sm font-medium border border-gray-300 rounded-l-lg ${currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
            btnPrev.disabled = currentPage === 1;
            btnPrev.onclick = () => { currentPage--; updateTable(); };
            container.appendChild(btnPrev);

            // Tombol Angka Halaman
            for (let i = 1; i <= totalPages; i++) {
                // Untuk mencegah tombol terlalu banyak (Optional jika data sangat besar)
                if(totalPages > 7 && (i > 3 && i < totalPages - 2) && i !== currentPage) {
                    if(i === 4 || i === totalPages - 3) {
                        const dots = document.createElement('span');
                        dots.innerText = '...';
                        dots.className = 'px-3 py-2 text-sm font-medium border-t border-b border-gray-300 bg-white text-gray-400 border-l';
                        container.appendChild(dots);
                    }
                    continue;
                }

                const btnPage = document.createElement('button');
                btnPage.innerText = i;
                btnPage.className = `px-3 py-2 text-sm font-medium border-t border-b border-gray-300 ${currentPage === i ? 'bg-blue-100 text-blue-800 border-l border-r border-blue-300 z-10 font-bold' : 'bg-white text-gray-700 hover:bg-gray-50 border-l'}`;
                btnPage.onclick = () => { currentPage = i; updateTable(); };
                container.appendChild(btnPage);
            }

            // Tombol Next
            const btnNext = document.createElement('button');
            btnNext.innerHTML = '<i class="fa-solid fa-chevron-right text-xs"></i>';
            btnNext.className = `px-3 py-2 text-sm font-medium border border-gray-300 rounded-r-lg border-l-0 ${currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
            btnNext.disabled = currentPage === totalPages;
            btnNext.onclick = () => { currentPage++; updateTable(); };
            container.appendChild(btnNext);
        }

        // Listener agar langsung bekerja saat user mengetik atau ganti filter tabel
        searchInput.addEventListener('input', () => { currentPage = 1; updateTable(); });
        perPageSelect.addEventListener('change', (e) => { 
            perPage = parseInt(e.target.value); 
            currentPage = 1; 
            updateTable(); 
        });

        // Panggil pertama kali saat halaman dimuat
        updateTable();
    });
</script>
@endsection