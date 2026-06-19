@extends('layouts.app')

@section('title', 'Perekaman Wajah')
@section('page_title', 'Data Perekaman Wajah Guru')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body, .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="font-poppins space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Daftar Perekaman Wajah (Face Enrollment)</h3>
            <p class="text-sm text-gray-500 mt-1">Kelola dan perbarui data biometrik wajah para guru.</p>
        </div>
        <div class="bg-blue-50 border border-blue-100 text-blue-700 px-4 py-3 rounded-xl text-sm flex items-center shadow-sm font-medium">
            <i class="fa-solid fa-circle-info mr-2 text-blue-500 text-lg"></i>
            <span>Pastikan pencahayaan terang dan stabil saat merekam wajah.</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-blue-50 text-[#1e3b8b] flex items-center justify-center text-2xl shadow-inner">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Guru</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $totalGuru }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-5">
                <i class="fa-solid fa-face-smile text-8xl"></i>
            </div>
            <div class="w-14 h-14 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-2xl shadow-inner">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Sudah Terdaftar</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $sudahRekam }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-5">
                <i class="fa-solid fa-face-frown text-8xl"></i>
            </div>
            <div class="w-14 h-14 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-2xl shadow-inner">
                <i class="fa-solid fa-camera"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Belum Rekam</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $belumRekam }}</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        
        <form method="GET" action="{{ route('admin.face.index') }}" class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            
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
                    <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>Sudah Terdaftar</option>
                    <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Rekam</option>
                </select>
                
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3b8b] focus:border-[#1e3b8b] block w-full pl-10 p-2" placeholder="Cari nama atau NIK...">
                </div>
                
                <button type="submit" class="bg-[#1e3b8b] hover:bg-[#152b69] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                    Cari
                </button>
                
                @if(request()->has('search') || request()->has('status'))
                <a href="{{ route('admin.face.index') }}" class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg text-sm font-semibold transition-colors border border-red-200" title="Reset Filter">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#243c94] text-white">
                    <tr>
                        <th class="px-5 py-4 font-semibold w-16">No</th>
                        <th class="px-5 py-4 font-semibold">Nama Guru & NIK</th>
                        <th class="px-5 py-4 font-semibold">Jabatan</th>
                        <th class="px-5 py-4 font-semibold">Status Wajah</th>
                        <th class="px-5 py-4 font-semibold text-center w-40">Aksi Perekaman</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($gurus as $index => $item)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-5 py-4 text-gray-600">{{ $gurus->firstItem() + $index }}</td>
                        <td class="px-5 py-4">
                            <div class="font-bold text-gray-800">{{ $item->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5"><i class="fa-regular fa-id-card mr-1 text-gray-400"></i> {{ $item->nik ?? $item->username }}</div>
                        </td>
                        <td class="px-5 py-4 text-gray-700 font-medium">
                            {{ $item->jabatan ?? 'Guru' }}
                        </td>
                        <td class="px-5 py-4">
                            @if($item->face_descriptor)
                                <div class="flex items-center gap-2 bg-green-50 px-3 py-1.5 rounded-lg border border-green-100 w-fit">
                                    <span class="relative flex h-2.5 w-2.5">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                    </span>
                                    <span class="text-green-700 font-bold text-xs">Terekam</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100 w-fit">
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                    <span class="text-red-600 font-bold text-xs">Belum Ada</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($item->face_descriptor)
                                <a href="{{ route('admin.face.record', $item->id) }}" class="inline-flex items-center justify-center bg-orange-50 text-orange-600 border border-orange-200 hover:bg-orange-100 px-4 py-2 rounded-lg transition-all text-xs font-bold w-full shadow-sm" title="Wajah sudah terekam. Klik untuk rekam ulang.">
                                    <i class="fa-solid fa-camera-rotate mr-2"></i> Rekam Ulang
                                </a>
                            @else
                                <a href="{{ route('admin.face.record', $item->id) }}" class="inline-flex items-center justify-center bg-[#1e3b8b] text-white hover:bg-[#152b69] px-4 py-2 rounded-lg transition-all text-xs font-bold w-full shadow-sm" title="Mulai Perekaman">
                                    <i class="fa-solid fa-camera mr-2"></i> Rekam Baru
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-users-slash text-5xl mb-4 text-gray-300"></i>
                                <p class="font-bold text-gray-700 text-lg">Tidak ada data guru ditemukan.</p>
                                <p class="text-sm mt-1">Silakan sesuaikan filter pencarian atau tambahkan data guru baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-100 pt-4">
            <div class="text-sm text-gray-500">
                Menampilkan <span class="font-bold text-gray-800">{{ $gurus->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-gray-800">{{ $gurus->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-800">{{ $gurus->total() }}</span> guru.
            </div>
            <div>
                {{ $gurus->links() }}
            </div>
        </div>
        
    </div>
</div>
@endsection