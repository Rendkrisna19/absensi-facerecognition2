@extends('layouts.app')

@section('title', 'Jaringan WiFi')
@section('page_title', 'Jaringan WiFi & QR Code')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="w-full font-poppins space-y-6">
    
    {{-- Card: Status Server + QR Link --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-{{ $ipServerCocok ? 'green' : 'red' }}-50 text-{{ $ipServerCocok ? 'green' : 'red' }}-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-{{ $ipServerCocok ? 'server' : 'triangle-exclamation' }}"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Server</p>
                    <p class="text-sm font-bold text-gray-800">IP: <span class="font-mono text-blue-600">{{ $serverIp }}</span></p>
                </div>
            </div>
            @if($ipServerCocok)
                <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700 font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                    Server terhubung ke jaringan <strong>{{ $jaringanCocok }}</strong>
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-600 font-medium">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    IP server belum terdaftar di jaringan WiFi. Tambahkan jaringan di bawah.
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">QR Code Absensi</p>
                <p class="text-sm text-gray-600 mb-4">Tampilkan QR Code di layar monitor PC sekolah untuk pintu masuk absensi guru.</p>
            </div>
            <a href="{{ route('qr-absensi') }}" target="_blank" class="w-full bg-[#002D8B] hover:bg-[#001a52] text-white px-5 py-3 rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-qrcode text-lg"></i> Buka Halaman QR Code
                <i class="fa-solid fa-up-right-from-square text-xs opacity-60"></i>
            </a>
        </div>
    </div>

    {{-- Header + Tombol Tambah --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Daftar Jaringan WiFi</h3>
            <p class="text-sm text-gray-500 mt-1">Hanya guru yang terhubung ke jaringan WiFi di bawah ini yang dapat melakukan absensi.</p>
        </div>
        <a href="{{ route('admin.pengaturan-lan.create') }}" class="bg-[#1e3b8b] hover:bg-[#152b69] text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm whitespace-nowrap flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Jaringan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center">
            <i class="fa-solid fa-check-circle mr-2 text-lg"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        
        <form method="GET" action="{{ route('admin.pengaturan-lan.index') }}" class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Tampilkan</span>
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-2 py-1 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
                <span>Data</span>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none bg-white">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
                
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3b8b] focus:border-[#1e3b8b] block w-full pl-10 p-2" placeholder="Cari Jaringan atau IP...">
                </div>
                
                <button type="submit" class="bg-[#1e3b8b] hover:bg-[#152b69] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                    Cari
                </button>
            </div>
        </form>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-[#243c94] text-white">
                    <tr>
                        <th class="px-5 py-4 font-semibold w-16">No</th>
                        <th class="px-5 py-4 font-semibold">Nama Jaringan</th>
                        <th class="px-5 py-4 font-semibold">IP Address Gateway</th>
                        <th class="px-5 py-4 font-semibold">Keterangan</th>
                        <th class="px-5 py-4 font-semibold text-center w-32">Status Absensi</th>
                        <th class="px-5 py-4 font-semibold text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($ips as $index => $item)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-5 py-4 text-gray-600">{{ $ips->firstItem() + $index }}</td>
                        <td class="px-5 py-4 font-bold text-gray-800">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                    <i class="fa-solid fa-wifi"></i>
                                </div>
                                {{ $item->nama_jaringan }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-mono bg-gray-50 text-blue-700 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold tracking-wide">
                                {{ $item->ip_address }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ $item->keterangan ?? '-' }}</td>
                        
                        <td class="px-5 py-4 text-center">
                            <label class="relative inline-flex items-center justify-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer toggle-status" data-id="{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500 shadow-inner"></div>
                            </label>
                            <div class="mt-1 status-label-{{ $item->id }} text-[10px] font-semibold {{ $item->is_active ? 'text-green-500' : 'text-gray-400' }}">
                                {{ $item->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                            </div>
                        </td>

                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.pengaturan-lan.edit', $item->id) }}" class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100 hover:border-transparent" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                
                                <button type="button" onclick="confirmDelete('{{ $item->id }}')" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm border border-red-100 hover:border-transparent" title="Hapus">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                                
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.pengaturan-lan.destroy', $item->id) }}" method="POST" class="hidden" style="display: none;">
                                    @csrf 
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-12 text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-network-wired text-5xl mb-4 text-gray-300"></i>
                                <p class="font-bold text-gray-700 text-lg">Belum ada konfigurasi jaringan.</p>
                                <p class="text-sm mt-1">Silakan tambahkan IP/Jaringan yang diizinkan untuk absen.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-100">
            <div class="text-sm text-gray-500">
                Menampilkan <span class="font-bold text-gray-800">{{ $ips->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-gray-800">{{ $ips->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-gray-800">{{ $ips->total() }}</span> data.
            </div>
            <div>
                {{ $ips->links() }}
            </div>
        </div>

    </div>
</div>
@endsection



<script>
    // --- SweetAlert Hapus ---
    function confirmDelete(id) {
        Swal.fire({
            title: '<span class="font-poppins">Hapus Jaringan?</span>',
            html: '<span class="font-poppins text-sm">Jaringan ini akan dihapus dari daftar IP yang diizinkan!</span>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'font-poppins rounded-lg',
                cancelButton: 'font-poppins rounded-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- AJAX Toggle Switch Aktif/Non-Aktif (Setiap IP) ---
        const toggleCheckboxes = document.querySelectorAll('.toggle-status');
        
        toggleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const ipId = this.getAttribute('data-id');
                const isChecked = this.checked;
                const labelStatus = document.querySelector(`.status-label-${ipId}`);

                labelStatus.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-gray-400"></i>';

                fetch(`/admin/pengaturan-lan/toggle/${ipId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if(data.success) {
                        labelStatus.innerText = data.is_active ? 'AKTIF' : 'NON-AKTIF';
                        labelStatus.className = data.is_active 
                            ? `mt-1 status-label-${ipId} text-[10px] font-semibold text-green-500` 
                            : `mt-1 status-label-${ipId} text-[10px] font-semibold text-gray-400`;
                        
                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'success',
                            title: '<span class="font-poppins text-sm">' + data.message + '</span>',
                            showConfirmButton: false, timer: 2000, timerProgressBar: true,
                        });
                    } else {
                        this.checked = !isChecked;
                        labelStatus.innerText = !isChecked ? 'AKTIF' : 'NON-AKTIF';
                        Swal.fire('Error', 'Gagal merubah status', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !isChecked;
                    labelStatus.innerText = !isChecked ? 'AKTIF' : 'NON-AKTIF';
                    Swal.fire('Oops...', 'Terjadi kesalahan sistem atau rute tidak ditemukan!', 'error');
                });
            });
        });

    });
</script>
