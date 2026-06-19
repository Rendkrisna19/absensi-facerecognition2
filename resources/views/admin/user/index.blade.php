@extends('layouts.app') 
@section('title', 'Manajemen Pengguna')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body, .font-poppins { font-family: 'Poppins', sans-serif !important; }
    
    /* Custom Scrollbar for Table */
    .table-scroll::-webkit-scrollbar { height: 8px; }
    .table-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 8px; }
    .table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
    .table-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endpush

@section('content')
<div class="font-poppins pb-8 space-y-6">

    <!-- Header Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 opacity-50 blur-3xl z-0"></div>
        
        <div class="flex items-center gap-5 relative z-10">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-2xl text-blue-600 shadow-inner">
                <i class="fa-solid fa-users-gear text-3xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Daftar Akun Sistem</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola hak akses admin, kepala yayasan, dan guru.</p>
            </div>
        </div>
        
        <div class="relative z-10 w-full md:w-auto">
            <a href="{{ route('admin.user.create') }}" class="w-full md:w-auto inline-flex items-center justify-center bg-[#1e3b8b] hover:bg-blue-800 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-lg shadow-blue-900/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-900/30 gap-2">
                <i class="fa-solid fa-plus bg-white/20 p-1.5 rounded-lg text-xs"></i> Tambah Data
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold flex items-center shadow-sm">
            <i class="fa-solid fa-circle-check text-xl mr-3 text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-sm font-bold flex items-center shadow-sm">
            <i class="fa-solid fa-triangle-exclamation text-xl mr-3 text-rose-500"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Main Content Box -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        
        <!-- Filter Toolbar -->
        <form method="GET" action="{{ route('admin.user.index') }}" class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100 mb-8 flex flex-col md:flex-row justify-between items-center gap-5">
            
            <div class="flex items-center gap-3 text-sm text-gray-600 w-full md:w-auto font-medium">
                <span>Tampilkan</span>
                <select name="per_page" onchange="this.form.submit()" class="border-none bg-white shadow-sm rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-[#1e3b8b]/20 outline-none cursor-pointer font-semibold text-gray-700 appearance-none pr-8 relative bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236B7280%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-[right_12px_center] bg-no-repeat">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>Data</span>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <select name="role" onchange="this.form.submit()" class="w-full sm:w-auto border-none bg-white shadow-sm rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-[#1e3b8b]/20 outline-none cursor-pointer appearance-none pr-8 relative bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236B7280%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-[right_12px_center] bg-no-repeat">
                    <option value="">Semua Status</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="kepala_yayasan" {{ request('role') == 'kepala_yayasan' ? 'selected' : '' }}>Yayasan</option>
                    <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                </select>
                
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-white border-none shadow-sm text-gray-900 text-sm font-medium rounded-xl focus:ring-2 focus:ring-[#1e3b8b]/20 block w-full pl-11 p-2.5 outline-none placeholder-gray-400" placeholder="Cari nama atau NIK...">
                </div>
                
                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="submit" class="flex-1 sm:flex-none bg-[#1e3b8b] hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm hover:shadow-md">
                        Cari
                    </button>
                    
                    @if(request()->has('search') || request()->has('role'))
                    <a href="{{ route('admin.user.index') }}" class="bg-rose-50 hover:bg-rose-100 text-rose-600 px-4 py-2.5 rounded-xl text-sm font-bold transition-all border border-rose-100 hover:border-rose-200 flex items-center justify-center" title="Reset Pencarian">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Data Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 table-scroll">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-[#243c94] text-white">
                    <tr>
                        <th class="px-5 py-4 font-semibold w-16 text-center">No <i class="fa-solid fa-sort ml-1 text-white/50 text-[10px]"></i></th>
                        <th class="px-5 py-4 font-semibold">Nama & Profil <i class="fa-solid fa-sort ml-1 text-white/50 text-[10px]"></i></th>
                        <th class="px-5 py-4 font-semibold">NIK <i class="fa-solid fa-sort ml-1 text-white/50 text-[10px]"></i></th>
                        <th class="px-5 py-4 font-semibold">Jabatan & Status <i class="fa-solid fa-sort ml-1 text-white/50 text-[10px]"></i></th>
                        <th class="px-5 py-4 font-semibold text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-blue-50/40 transition-colors duration-200 group">
                        <td class="px-5 py-4 text-gray-500 font-medium text-center">{{ $users->firstItem() + $index }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                <!-- Avatar -->
                                @php
                                    $fotoUrl = $user->foto_profil ? asset('storage/' . $user->foto_profil) : null;
                                @endphp
                                
                                @if($fotoUrl)
                                    <img src="{{ $fotoUrl }}" alt="Foto {{ $user->name }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shadow-sm group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-12 h-12 rounded-xl 
                                        @if($user->role == 'admin') bg-gradient-to-br from-purple-100 to-purple-200 text-purple-700
                                        @elseif($user->role == 'kepala_yayasan') bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700
                                        @else bg-gradient-to-br from-emerald-100 to-emerald-200 text-emerald-700
                                        @endif 
                                        flex items-center justify-center font-bold text-lg shadow-inner border border-gray-100 group-hover:scale-105 transition-transform duration-300">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                
                                <div>
                                    <p class="font-bold text-gray-800 text-base">{{ $user->name }}</p>
                                    <p class="text-[11px] text-gray-400 font-medium mt-0.5 tracking-wide"><i class="fa-solid fa-calendar-check text-gray-300 mr-1"></i> Terdaftar: {{ $user->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <!-- Only NIK displayed here -->
                            <div class="inline-flex items-center gap-2 bg-[#1e3b8b]/5 px-3 py-1.5 rounded-lg border border-[#1e3b8b]/10 shadow-sm group-hover:bg-[#1e3b8b]/10 transition-colors">
                                <div class="w-6 h-6 rounded-md bg-white flex items-center justify-center shadow-sm">
                                    <i class="fa-regular fa-id-card text-[#1e3b8b] text-[10px]"></i>
                                </div>
                                <span class="text-[#1e3b8b] font-poppins font-bold tracking-widest text-xs">{{ $user->nik ?? 'BELUM DIATUR' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($user->role == 'admin')
                                <span class="inline-flex items-center bg-purple-50 text-purple-600 border border-purple-100 px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500 mr-2 animate-pulse"></span> Admin
                                </span>
                            @elseif($user->role == 'kepala_yayasan')
                                <span class="inline-flex items-center bg-blue-50 text-blue-600 border border-blue-100 px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2"></span> Yayasan
                                </span>
                            @else
                                <span class="inline-flex items-center bg-emerald-50 text-emerald-600 border border-emerald-100 px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span> Guru
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex justify-center gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.user.edit', $user->id) }}" class="bg-white text-blue-600 hover:bg-blue-600 hover:text-white w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-300 shadow-sm border border-blue-100 hover:border-transparent hover:-translate-y-1 hover:shadow-md" title="Edit Akun">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" class="inline-block form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="bg-white text-rose-500 hover:bg-rose-500 hover:text-white w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-300 shadow-sm border border-rose-100 hover:border-transparent hover:-translate-y-1 hover:shadow-md btn-delete" title="Hapus Akun">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-16 text-center bg-gray-50/30">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 mb-4">
                                    <i class="fa-solid fa-users-slash text-4xl text-gray-300"></i>
                                </div>
                                <p class="font-bold text-gray-600 text-lg">Tidak ada data ditemukan</p>
                                <p class="text-sm mt-1 text-gray-400">Silakan ubah kata kunci pencarian atau filter status.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Section -->
        <div class="mt-8 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
            <div class="text-sm text-gray-500 font-medium">
                Menampilkan <span class="font-bold text-[#1e3b8b] bg-blue-50 px-2 py-0.5 rounded-md">{{ $users->firstItem() ?? 0 }}</span> 
                sampai <span class="font-bold text-[#1e3b8b] bg-blue-50 px-2 py-0.5 rounded-md">{{ $users->lastItem() ?? 0 }}</span> 
                dari total <span class="font-bold text-[#1e3b8b] bg-blue-50 px-2 py-0.5 rounded-md">{{ $users->total() }}</span> data.
            </div>
            <div class="pagination-wrapper">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- SWEETALERT DELETE ---
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.form-delete');
                Swal.fire({
                    title: '<span class="font-poppins font-bold">Hapus Akun?</span>',
                    html: '<span class="font-poppins text-sm text-gray-500">Akun ini tidak akan bisa login lagi ke dalam sistem. Data tidak dapat dikembalikan!</span>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f43f5e', // rose-500
                    cancelButtonColor: '#94a3b8', // slate-400
                    confirmButtonText: '<i class="fa-solid fa-trash-can mr-1.5"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-3xl shadow-xl border border-gray-100',
                        confirmButton: 'font-poppins font-bold rounded-xl px-5 py-2.5 transition-transform hover:scale-105',
                        cancelButton: 'font-poppins font-bold rounded-xl px-5 py-2.5 transition-transform hover:scale-105'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>