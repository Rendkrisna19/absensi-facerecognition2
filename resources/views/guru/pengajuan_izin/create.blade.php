@extends('layouts.mobile')

@section('title', 'Buat Pengajuan')
@section('page_title', 'Form Pengajuan')
@section('subtitle', 'Silakan isi data berikut')

@section('content')
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
        
        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-xl text-xs mb-4 border border-red-100">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('guru.pengajuan-izin.store') }}" method="POST" enctype="multipart/form-data" x-data="{ jenis: '{{ old('jenis', '') }}' }">
            @csrf
            
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jenis Pengajuan <span class="text-red-500">*</span></label>
                <select name="jenis" x-model="jenis" required class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-[#002D8B] focus:border-[#002D8B] block p-2.5 outline-none transition-colors">
                    <option value="" disabled selected>-- Pilih Jenis --</option>
                    <option value="Sakit" {{ old('jenis') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Izin" {{ old('jenis') == 'Izin' ? 'selected' : '' }}>Izin Keperluan</option>
                    <option value="Cuti" {{ old('jenis') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Dari Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai" required value="{{ old('tanggal_mulai', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-[#002D8B] focus:border-[#002D8B] block p-2.5 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Sampai Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_selesai" required value="{{ old('tanggal_selesai', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-[#002D8B] focus:border-[#002D8B] block p-2.5 outline-none">
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mb-4 -mt-2">*Jika izin hanya 1 hari, samakan tanggal dari dan sampai.</p>

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Keterangan / Alasan <span class="text-red-500">*</span></label>
                <textarea name="alasan" required rows="3" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-[#002D8B] focus:border-[#002D8B] block p-2.5 outline-none resize-none" placeholder="Tuliskan alasan dengan jelas...">{{ old('alasan') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Lampiran Bukti 
                    <span x-show="jenis == 'Sakit'" class="text-red-500">*</span>
                    <span x-show="jenis != 'Sakit'" class="font-normal text-gray-500">(Opsional)</span>
                </label>
                <input type="file" name="file_bukti" :required="jenis == 'Sakit'" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#002D8B] hover:file:bg-blue-100">
                <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, PDF (Maks 2MB). Wajib untuk Sakit.</p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('guru.pengajuan-izin.index') }}" class="flex-1 text-center py-3 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 active:scale-95 transition">Batal</a>
                <button type="submit" class="flex-[2] text-center py-3 bg-[#002D8B] text-white text-sm font-semibold rounded-xl hover:bg-[#001f63] active:scale-95 transition shadow-lg shadow-blue-900/20">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
@endsection