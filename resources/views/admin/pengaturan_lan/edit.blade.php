@extends('layouts.app')

@section('title', 'Edit LAN')
@section('page_title', 'Edit Jaringan LAN')

@push('styles')
<style>
    .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 font-poppins">
    
    <div class="mb-8 border-b border-gray-100 pb-4">
        <h4 class="text-xl font-bold text-gray-800">Edit Jaringan LAN</h4>
        <p class="text-sm text-gray-500 mt-1">Perbarui detail konfigurasi jaringan untuk absensi.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6 text-sm">
            <div class="flex items-center gap-2 font-bold mb-2">
                <i class="fa-solid fa-triangle-exclamation"></i> Terdapat Kesalahan:
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pengaturan-lan.update', $ip->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Jaringan / Lokasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_jaringan" value="{{ old('nama_jaringan', $ip->nama_jaringan) }}" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    IP Address <span class="text-red-500">*</span>
                </label>
                <input type="text" name="ip_address" value="{{ old('ip_address', $ip->ip_address) }}" required class="w-full font-mono border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all text-blue-700 bg-blue-50/30">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status Jaringan</label>
                <div class="relative">
                    <select name="is_active" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all appearance-none bg-white">
                        <option value="1" {{ old('is_active', $ip->is_active) == '1' ? 'selected' : '' }}>Aktif (Diizinkan Absen)</option>
                        <option value="0" {{ old('is_active', $ip->is_active) == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                        <i class="fa-solid fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan Tambahan <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all">{{ old('keterangan', $ip->keterangan) }}</textarea>
            </div>
        </div>

        <div class="flex flex-col-reverse md:flex-row justify-end gap-3 pt-6 mt-8 border-t border-gray-100">
            <a href="{{ route('admin.pengaturan-lan.index') }}" class="w-full md:w-auto px-6 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-colors text-sm text-center">
                Batal
            </a>
            <button type="submit" class="w-full md:w-auto px-6 py-2.5 rounded-xl bg-[#1e3b8b] hover:bg-[#152b69] text-white font-semibold transition-colors shadow-sm flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-check-double"></i> Perbarui Data
            </button>
        </div>
    </form>
</div>
@endsection