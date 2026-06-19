@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Pengaturan Profil')

@push('styles')
<style>
    .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="font-poppins w-full min-h-[calc(100vh-130px)] flex flex-col">
    
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm font-medium flex items-center">
            <i class="fa-solid fa-check-circle mr-2 text-lg"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex-1 flex flex-col">
        <div class="px-6 md:px-8 py-6 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Detail Profil Akun</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola informasi pribadi dan keamanan akun Anda.</p>
            </div>
            <span class="bg-[#002D8B]/10 text-[#002D8B] px-4 py-1.5 rounded-lg font-bold text-xs uppercase tracking-wider">
                {{ str_replace('_', ' ', $user->role) }}
            </span>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
            @csrf
            @method('PUT')
            
            <div class="flex flex-col md:flex-row gap-8">
                
                <div class="w-full md:w-1/3 flex flex-col items-center">
                    <div class="relative group cursor-pointer mb-4">
                        <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-100 flex items-center justify-center">
                            <img id="photo-preview" 
                                 src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=002D8B&color=fff&size=256' }}" 
                                 alt="Profile Photo" 
                                 class="w-full h-full object-cover">
                        </div>
                        
                        <label for="foto_profil" class="absolute inset-0 bg-black/40 rounded-full flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <i class="fa-solid fa-camera text-2xl mb-1"></i>
                            <span class="text-xs font-semibold">Ubah Foto</span>
                        </label>
                    </div>
                    
                    <input type="file" name="foto_profil" id="foto_profil" class="hidden" accept="image/jpeg, image/png, image/jpg" onchange="previewImage(event)">
                    
                    <p class="text-xs text-gray-400 text-center px-4">
                        Format .jpg, .jpeg, .png<br>Maksimal ukuran 2MB
                    </p>
                    @error('foto_profil')
                        <p class="text-xs text-red-500 font-medium mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="w-full md:w-2/3 space-y-6">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all" required>
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Username / NIK</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all">
                        @error('username') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-blue-50/50 p-5 rounded-xl border border-blue-100 mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ganti Password (Opsional)</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all pr-10" placeholder="Kosongkan jika tidak ingin diubah">
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-[#1e3b8b]">
                                <i class="fa-regular fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2"><i class="fa-solid fa-circle-info mr-1 text-[#002D8B]"></i> Biarkan kosong jika Anda hanya ingin mengubah data di atas.</p>
                        @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-100">
                        <button type="submit" class="px-8 py-3 rounded-xl bg-[#1e3b8b] hover:bg-[#152b69] text-white font-bold transition-colors shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Profil
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Live Preview Gambar Profil
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle Lihat/Sembunyikan Password
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection