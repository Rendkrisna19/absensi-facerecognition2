@extends('layouts.mobile')

@section('title', 'Pengaturan')
@section('subtitle', 'Kelola Akun')
@section('page_title', 'Profil Saya')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center shadow-sm">
            <i class="fa-solid fa-check-circle mr-2 text-lg"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Form Update Profil -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-[#002D8B] to-[#001f63]"></div>
        
        <form action="{{ route('guru.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Foto Profil Area -->
            <div class="relative z-10 flex flex-col items-center mt-6 mb-8">
                <div class="relative group cursor-pointer" onclick="document.getElementById('foto_profil').click()">
                    <!-- Gambar Profil -->
                    <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                        @if($user->foto_profil)
                            <img id="preview-image" src="{{ asset('storage/' . $user->foto_profil) }}" alt="Profil" class="w-full h-full object-cover">
                        @else
                            <img id="preview-image" src="" alt="Profil" class="w-full h-full object-cover hidden">
                            <span id="initial-avatar" class="text-3xl font-bold text-[#002D8B]">{{ substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <!-- Badge Kamera -->
                    <div class="absolute bottom-0 right-0 bg-[#002D8B] text-white w-8 h-8 rounded-full border-2 border-white flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-camera text-xs"></i>
                    </div>
                </div>
                <input type="file" id="foto_profil" name="foto_profil" accept="image/*" class="hidden" onchange="previewFile(this)">
                <p class="text-[10px] text-gray-400 mt-3 text-center">Format: JPG, PNG (Maks 2MB).<br>Klik foto untuk mengubah.</p>
            </div>

            <!-- Input Form -->
            <div class="space-y-4">
                <!-- Field Readonly -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1 ml-1">Nama Lengkap</label>
                    <input type="text" value="{{ $user->name }}" readonly class="w-full bg-gray-50 border border-gray-200 text-gray-500 cursor-not-allowed rounded-xl px-4 py-3 font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1 ml-1">NIK / Username</label>
                    <input type="text" value="{{ $user->nik }}" readonly class="w-full bg-gray-50 border border-gray-200 text-gray-500 cursor-not-allowed rounded-xl px-4 py-3 font-medium focus:outline-none">
                </div>

                <!-- Field Editable: Nomor HP -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 ml-1">Nomor Handphone <span class="text-red-500">*</span></label>
                    <input type="number" name="no_hp" value="{{ old('no_hp', $user->guru->no_hp ?? '') }}" required placeholder="Contoh: 081234567890" class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 font-semibold focus:outline-none focus:ring-2 focus:ring-[#002D8B] focus:border-[#002D8B] transition-all shadow-sm">
                    @error('no_hp')
                        <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" class="w-full mt-6 bg-[#002D8B] hover:bg-[#001f63] text-white font-bold py-3.5 rounded-xl transition-all shadow-md active:scale-95 flex justify-center items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Tombol Keluar -->
    <form action="{{ route('logout') }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="w-full bg-red-50 border border-red-100 text-red-600 hover:bg-red-500 hover:text-white font-bold py-3.5 rounded-xl transition-all flex justify-center items-center gap-2">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar Aplikasi
        </button>
    </form>

    <div class="h-6"></div>
</div>

@push('scripts')
<script>
    function previewFile(input) {
        var file = input.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-image').src = e.target.result;
                document.getElementById('preview-image').classList.remove('hidden');
                
                var initialAvatar = document.getElementById('initial-avatar');
                if(initialAvatar) {
                    initialAvatar.classList.add('hidden');
                }
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
@endsection