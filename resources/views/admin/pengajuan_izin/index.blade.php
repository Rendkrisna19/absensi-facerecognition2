@extends('layouts.app')

@section('title', 'Validasi Pengajuan Izin')
@section('page_title', 'Validasi Izin & Cuti')

@section('content')
<div class="mb-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-l-yellow-400">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Menunggu Validasi</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalPending }} <span class="text-sm font-normal text-gray-400">Pengajuan</span></h3>
            </div>
            <div class="w-12 h-12 bg-yellow-50 text-yellow-500 rounded-full flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-l-green-500">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Izin Disetujui</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalDisetujui }} <span class="text-sm font-normal text-gray-400">Pengajuan</span></h3>
            </div>
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-check-double"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-l-red-500">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Izin Ditolak</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalDitolak }} <span class="text-sm font-normal text-gray-400">Pengajuan</span></h3>
            </div>
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-ban"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
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
            <h3 class="text-xl font-bold text-gray-800">Daftar Pengajuan</h3>
            <p class="text-sm text-gray-500 mt-1">Kelola permohonan Sakit, Izin, dan Cuti pegawai.</p>
        </div>
        <div class="flex gap-2" x-data="{ openCleanup: false }">
            <button @click="openCleanup = true" type="button" class="bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200 px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-broom"></i> Bersihkan Data
            </button>

            <!-- Modal Cleanup Data Izin -->
            <template x-teleport="body">
                <div x-show="openCleanup" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                    <div x-show="openCleanup" x-transition.opacity @click="openCleanup = false" class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm"></div>
                    <div x-show="openCleanup" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden z-10">
                        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold flex items-center gap-2 text-gray-800"><i class="fa-solid fa-broom text-[#002D8B]"></i> Bersihkan Data Izin</h3>
                            <button @click="openCleanup = false" class="text-gray-400 hover:text-gray-600 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        <form id="form-cleanup-izin" action="{{ route('admin.pengajuan-izin.cleanup') }}" method="POST" class="p-5" x-data="{ tipeHapus: 'hari' }">
                            @csrf
                            <div class="bg-red-50 border border-red-200 text-red-600 text-xs px-3 py-2 rounded-lg mb-4 flex gap-2">
                                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                                <span>Tindakan ini akan menghapus data beserta <b>file bukti fisik</b> secara permanen.</span>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">Mode Pembersihan</label>
                                <select name="tipe_hapus" x-model="tipeHapus" class="w-full border-2 border-gray-300 rounded-xl text-sm focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5 transition bg-gray-50 hover:bg-white cursor-pointer font-medium">
                                    <option value="hari">Hapus 1 Hari Tertentu</option>
                                    <option value="minggu">Hapus 1 Minggu Tertentu</option>
                                    <option value="semua">Hapus Semua Data Izin</option>
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
                                <button type="button" onclick="confirmCleanupIzin()" class="px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 transition-colors shadow-sm flex items-center gap-1.5">
                                    <i class="fa-solid fa-trash-can"></i> Hapus Permanen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100 mb-4 gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <span class="text-sm text-gray-600 font-medium">Tampilkan</span>
            <select id="perPage" class="border-gray-300 rounded-lg text-sm focus:ring-[#002D8B] focus:border-[#002D8B] w-20">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span class="text-sm text-gray-600 font-medium">Data</span>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
            <select id="filterStatus" class="border-gray-300 rounded-lg text-sm focus:ring-[#002D8B] focus:border-[#002D8B] w-full md:w-40">
                <option value="all">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
            
            <div class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#002D8B] focus:border-[#002D8B] block w-full pl-10 p-2.5" placeholder="Cari nama atau alasan...">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left border-collapse" id="dataTable">
            <thead>
                <tr class="bg-[#002D8B] text-white text-sm border-b border-[#001f63] cursor-pointer select-none">
                    <th class="p-3 font-semibold hover:bg-[#001f63] transition" onclick="sortTable(0)">Nama Pegawai <i class="fa-solid fa-sort ml-1 text-blue-300"></i></th>
                    <th class="p-3 font-semibold hover:bg-[#001f63] transition" onclick="sortTable(1)">Jenis Izin <i class="fa-solid fa-sort ml-1 text-blue-300"></i></th>
                    <th class="p-3 font-semibold">Tanggal & Alasan</th>
                    <th class="p-3 font-semibold hover:bg-[#001f63] transition" onclick="sortTable(3)">Status <i class="fa-solid fa-sort ml-1 text-blue-300"></i></th>
                    <th class="p-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm bg-white" id="tableBody">
                @forelse($pengajuanIzins as $item)
                @php
                    $statusColor = $item->status == 'Disetujui' ? 'bg-green-100 text-green-700' : ($item->status == 'Ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                    $fotoUrl = $item->user->foto_profil ? asset('storage/' . $item->user->foto_profil) : asset('images/default-avatar.png');
                @endphp

                <tr class="data-row border-b border-gray-100 hover:bg-blue-50 transition-colors" 
                    x-data="{ actOpen: false }"
                    data-search="{{ strtolower($item->user->name . ' ' . $item->jenis . ' ' . $item->alasan) }}"
                    data-status="{{ strtolower($item->status) }}">
                    
                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $fotoUrl }}" alt="Foto" class="w-10 h-10 rounded-full object-cover border border-gray-300 shadow-sm">
                            <div>
                                <div class="font-bold text-gray-800">{{ $item->user->name }}</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">Diajukan: {{ $item->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                    </td>

                    <td class="p-3">
                        <span class="inline-block text-xs font-bold px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 border border-gray-200">
                            {{ $item->jenis }}
                        </span>
                        @if($item->file_bukti)
                            <button type="button" @click="$dispatch('open-image-modal', '{{ asset('storage/' . $item->file_bukti) }}')" class="block mt-1.5 text-[11px] text-blue-600 hover:underline text-left">
                                <i class="fa-solid fa-image"></i> Lihat Bukti
                            </button>
                        @else
                            <p class="mt-1.5 text-[11px] text-gray-400"><i class="fa-solid fa-xmark"></i> Tdk ada file</p>
                        @endif
                    </td>

                    <td class="p-3">
                        <div class="font-semibold text-gray-800 text-xs mb-1">
                            <i class="fa-regular fa-calendar text-[#002D8B] mr-1"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                            @if($item->tanggal_mulai != $item->tanggal_selesai)
                                s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 line-clamp-2 max-w-xs" title="{{ $item->alasan }}">{{ $item->alasan }}</p>
                    </td>

                    <td class="p-3">
                        <span class="text-[11px] px-2.5 py-1 rounded-full font-bold {{ $statusColor }}">
                            {{ $item->status }}
                        </span>
                        @if($item->status == 'Ditolak')
                            <p class="text-[10px] text-red-500 mt-1 line-clamp-1" title="{{ $item->catatan_penolakan }}">{{ $item->catatan_penolakan }}</p>
                        @endif
                    </td>

                    <td class="p-3 text-center">
                        @if($item->status == 'Pending')
                            <button @click="actOpen = true" class="px-3 py-1.5 bg-[#002D8B] text-white text-xs font-semibold rounded-lg hover:bg-[#001f63] transition shadow-sm">
                                Respon
                            </button>
                        @else
                            <span class="text-xs text-gray-400 italic">Selesai</span>
                        @endif

                        <template x-teleport="body">
                            <div x-show="actOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                <div x-show="actOpen" x-transition.opacity @click="actOpen = false" class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm"></div>
                                <div x-show="actOpen" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden z-10">
                                    <div class="px-5 py-4 border-b bg-[#002D8B] text-white flex justify-between items-center">
                                        <h3 class="font-bold">Respon Pengajuan</h3>
                                        <button @click="actOpen = false" class="hover:text-gray-300"><i class="fa-solid fa-xmark text-xl"></i></button>
                                    </div>
                                    <form action="{{ route('admin.pengajuan-izin.status', $item->id) }}" method="POST" class="p-5" x-data="{ statusRespon: 'Disetujui' }">
                                        @csrf
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600 mb-1">Keputusan untuk izin <strong>{{ $item->user->name }}</strong>:</p>
                                            <select name="status" x-model="statusRespon" class="w-full border-gray-300 rounded-xl text-sm focus:ring-[#002D8B]">
                                                <option value="Disetujui">Setujui Pengajuan</option>
                                                <option value="Ditolak">Tolak Pengajuan</option>
                                            </select>
                                        </div>
                                        <div class="mb-5" x-show="statusRespon == 'Ditolak'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan</label>
                                            <textarea name="catatan_penolakan" rows="2" class="w-full border-gray-300 rounded-xl text-sm focus:ring-red-500" placeholder="Tulis alasan kenapa ditolak..."></textarea>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="actOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200">Batal</button>
                                            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors" :class="statusRespon == 'Disetujui' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'">Simpan Keputusan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </template>
                    </td>
                </tr>
                @empty
                <tr id="emptyState" class="border-b border-gray-100">
                    <td colspan="5" class="text-center p-12">
                        <div class="text-gray-300 mb-3"><i class="fa-solid fa-folder-open text-5xl"></i></div>
                        <p class="text-gray-600 font-bold text-lg">Tidak ada data</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center mt-6 gap-4" id="paginationControls">
        <div class="text-sm text-gray-600 font-medium">
            Menampilkan <span id="infoStart" class="font-bold text-gray-900">0</span> sampai <span id="infoEnd" class="font-bold text-gray-900">0</span> dari <span id="infoTotal" class="font-bold text-gray-900">0</span> data
        </div>
        <div class="inline-flex rounded-md shadow-sm" id="paginationButtons"></div>
    </div>
</div>

<!-- Modal Gambar Alpine -->
<div x-data="{ open: false, imageUrl: '' }" 
     @open-image-modal.window="open = true; imageUrl = $event.detail"
     x-show="open" 
     style="display: none;" 
     class="fixed inset-0 z-[110] flex items-center justify-center p-4">
    
    <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-black bg-opacity-70 backdrop-blur-sm"></div>
    
    <div x-show="open" x-transition.scale.origin.center class="relative z-10 max-w-3xl w-full">
        <div class="absolute -top-12 right-0 flex gap-3">
            <a :href="imageUrl" download="Bukti_Izin.jpg" class="text-white bg-blue-600 hover:bg-blue-700 rounded-full w-10 h-10 flex items-center justify-center focus:outline-none transition shadow-lg" title="Download Bukti">
                <i class="fa-solid fa-download"></i>
            </a>
            <button @click="open = false" class="text-white hover:text-gray-300 focus:outline-none w-10 h-10 flex items-center justify-center bg-white/20 hover:bg-white/30 rounded-full backdrop-blur-sm transition shadow-lg" title="Tutup">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <img :src="imageUrl" class="w-full h-auto max-h-[85vh] object-contain rounded-lg shadow-2xl bg-white/5" alt="Bukti Lampiran">
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('tableBody');
        const rows = Array.from(document.querySelectorAll('.data-row'));
        const searchInput = document.getElementById('searchInput');
        const perPageSelect = document.getElementById('perPage');
        const filterStatusSelect = document.getElementById('filterStatus');
        
        let currentPage = 1;
        let perPage = parseInt(perPageSelect.value);
        let filteredRows = [...rows];
        let sortDirection = false;

        function updateTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusFilter = filterStatusSelect.value.toLowerCase();

            filteredRows = rows.filter(row => {
                const textData = row.getAttribute('data-search');
                const rowStatus = row.getAttribute('data-status');
                const matchSearch = textData.includes(searchTerm);
                const matchStatus = (statusFilter === 'all') || (rowStatus === statusFilter);
                return matchSearch && matchStatus;
            });

            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / perPage);
            if (currentPage > totalPages) currentPage = totalPages || 1;

            const startIdx = (currentPage - 1) * perPage;
            const endIdx = startIdx + perPage;

            rows.forEach(row => row.style.display = 'none');

            filteredRows.slice(startIdx, endIdx).forEach(row => {
                row.style.display = '';
            });

            document.getElementById('infoStart').innerText = totalRows === 0 ? 0 : startIdx + 1;
            document.getElementById('infoEnd').innerText = Math.min(endIdx, totalRows);
            document.getElementById('infoTotal').innerText = totalRows;

            generatePagination(totalPages);
            
            // Handle Empty State
            const emptyState = document.getElementById('emptyState');
            if (emptyState) {
                emptyState.style.display = totalRows === 0 ? '' : 'none';
            }
        }

        function generatePagination(totalPages) {
            const container = document.getElementById('paginationButtons');
            container.innerHTML = '';
            if (totalPages <= 1) return;

            const btnPrev = document.createElement('button');
            btnPrev.innerHTML = '<i class="fa-solid fa-chevron-left text-xs"></i>';
            btnPrev.className = `px-3 py-2 text-sm font-medium border border-gray-300 rounded-l-lg ${currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
            btnPrev.disabled = currentPage === 1;
            btnPrev.onclick = () => { currentPage--; updateTable(); };
            container.appendChild(btnPrev);

            for (let i = 1; i <= totalPages; i++) {
                const btnPage = document.createElement('button');
                btnPage.innerText = i;
                btnPage.className = `px-3 py-2 text-sm font-medium border-t border-b border-gray-300 ${currentPage === i ? 'bg-[#002D8B] text-white border-l border-r z-10' : 'bg-white text-gray-700 hover:bg-gray-50 border-l'}`;
                btnPage.onclick = () => { currentPage = i; updateTable(); };
                container.appendChild(btnPage);
            }

            const btnNext = document.createElement('button');
            btnNext.innerHTML = '<i class="fa-solid fa-chevron-right text-xs"></i>';
            btnNext.className = `px-3 py-2 text-sm font-medium border border-gray-300 rounded-r-lg border-l-0 ${currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
            btnNext.disabled = currentPage === totalPages;
            btnNext.onclick = () => { currentPage++; updateTable(); };
            container.appendChild(btnNext);
        }

        searchInput.addEventListener('input', () => { currentPage = 1; updateTable(); });
        filterStatusSelect.addEventListener('change', () => { currentPage = 1; updateTable(); });
        perPageSelect.addEventListener('change', (e) => { 
            perPage = parseInt(e.target.value); 
            currentPage = 1; 
            updateTable(); 
        });

        window.sortTable = function(columnIndex) {
            sortDirection = !sortDirection;
            rows.sort((a, b) => {
                let cellA = a.cells[columnIndex].innerText.trim().toLowerCase();
                let cellB = b.cells[columnIndex].innerText.trim().toLowerCase();
                if (cellA < cellB) return sortDirection ? 1 : -1;
                if (cellA > cellB) return sortDirection ? -1 : 1;
                return 0;
            });
            rows.forEach(row => tableBody.appendChild(row)); 
            updateTable();
        };

        updateTable();
    });

    function confirmCleanupIzin() {
        if(typeof Swal === 'undefined') {
            if(confirm('Apakah Anda yakin ingin menghapus data izin beserta file buktinya secara permanen?')) {
                document.getElementById('form-cleanup-izin').submit();
            }
            return;
        }
        Swal.fire({
            title: 'Bersihkan Data Izin?',
            text: "Data izin beserta file bukti fisiknya akan dihapus secara permanen dan tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', 
            cancelButtonColor: '#6b7280', 
            confirmButtonText: '<i class="fa-solid fa-trash-can mr-1"></i> Ya, Hapus Permanen!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-cleanup-izin').submit();
            }
        });
    }
</script>
@endsection