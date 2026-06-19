@extends('layouts.auth')

@section('title', 'Login - Sistem Absensi Tri Jaya')

@section('content')
<!-- Background Area dengan Ornamen Lingkaran -->
<div x-data="{ showModal: false }" class="relative flex min-h-screen w-full bg-[#EEF2F6] font-sans items-center justify-center p-4 sm:p-8 overflow-hidden">
    
    <!-- Ornamen Lingkaran Melayang (Sesuai gambar referensi) -->
    <div class="absolute top-[-5%] left-[-2%] w-48 h-48 sm:w-64 sm:h-64 bg-[#002D8B] rounded-full opacity-80 shadow-2xl"></div>
    <div class="absolute bottom-[-5%] right-[-2%] w-48 h-48 sm:w-64 sm:h-64 bg-white rounded-full shadow-xl"></div>

    <!-- Main Card -->
    <div class="relative z-10 flex flex-col lg:flex-row w-full max-w-[1000px] bg-white rounded-[2.5rem] shadow-2xl overflow-hidden min-h-[600px]">
        
        <!-- KIRI: Area Form Login -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-16">
            <div class="w-full max-w-sm mx-auto">
                
                <!-- Header Title -->
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-gray-900 tracking-wide uppercase mb-2">Login</h2>
                    <p class="text-gray-500 text-xs sm:text-sm">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Input Email/NIK -->
                    <div>
                        <label for="nik" class="block mb-2 text-xs font-semibold text-gray-600 ml-1">NIK</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-gray-400">
                                <i class="fa-regular fa-user"></i>
                            </div>
                            <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required autofocus
                                class="w-full pl-12 pr-4 py-3.5 bg-[#F3F6FA] border-none rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#002D8B] outline-none transition-all duration-300 text-gray-800 text-sm font-medium {{ $errors->has('nik') ? 'ring-2 ring-red-500' : '' }}"
                                placeholder="Masukkan NIK Anda">
                        </div>
                        @error('nik')
                            <p class="text-red-500 text-xs mt-2 font-medium flex items-center ml-2">
                                <i class="fa-solid fa-circle-exclamation mr-1.5"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Input Password -->
                    <div x-data="{ show: false }">
                        <label for="password" class="block mb-2 text-xs font-semibold text-gray-600 ml-1">Password</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-gray-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </div>
                            <input :type="show ? 'text' : 'password'" name="password" id="password" required
                                class="w-full pl-12 pr-12 py-3.5 bg-[#F3F6FA] border-none rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#002D8B] outline-none transition-all duration-300 text-gray-800 text-sm font-medium tracking-widest placeholder:tracking-normal {{ $errors->has('password') ? 'ring-2 ring-red-500' : '' }}"
                                placeholder="Password">
                            
                            <!-- Toggle Mata -->
                            <button type="button" @click="show = !show" 
                                class="absolute right-4 text-gray-400 hover:text-[#002D8B] transition-colors focus:outline-none">
                                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-2 font-medium flex items-center ml-2">
                                <i class="fa-solid fa-circle-exclamation mr-1.5"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" class="w-4 h-4 rounded border-gray-300 text-[#002D8B] focus:ring-[#002D8B]/30 transition-shadow cursor-pointer">
                            <label for="remember" class="ml-2 text-xs text-gray-500 font-medium cursor-pointer">Ingat saya</label>
                        </div>
                    
                    </div>

                    <!-- Button Login -->
                    <button type="submit" 
                        class="w-full bg-[#002D8B] hover:bg-[#001A52] text-white font-semibold py-3.5 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 mt-2 active:scale-95 text-sm uppercase tracking-wider">
                        Login Now
                    </button>
                </form>
            </div>
        </div>

        <!-- KANAN: Area Visual/Ilustrasi (Sesuai referensi gambar kanan) -->
        <div class="hidden lg:flex lg:w-1/2 bg-[#002D8B] relative items-center justify-center p-12 overflow-hidden">
            
            <!-- Pola Garis Gelombang Latar Belakang (Mirip di referensi) -->
            <div class="absolute inset-0 opacity-20">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0,50 Q100,100 200,50 T400,50 T600,50" stroke="white" stroke-width="2" fill="none"/>
                    <path d="M0,150 Q150,200 300,150 T600,150 T900,150" stroke="white" stroke-width="2" fill="none"/>
                    <path d="M0,250 Q200,300 400,250 T800,250 T1200,250" stroke="white" stroke-width="2" fill="none"/>
                </svg>
            </div>

            <!-- Wadah Kaca (Glassmorphism) untuk Lottie -->
            <div class="relative z-10 w-full max-w-sm bg-white/10 backdrop-blur-md border border-white/20 rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.12)]">
                
                <!-- Lottie Animasi -->
                <!-- PENTING: Jika logo.json error, sementara kamu bisa hapus atau cek foldernya -->
                <div class="w-full h-48 sm:h-56 flex items-center justify-center mb-6">
                    <lottie-player 
                        src="{{ asset('lottie/animasi.json') }}" 
                        background="transparent" 
                        speed="1" 
                        style="width: 100%; height: 100%;" 
                        loop 
                        autoplay>
                    </lottie-player>
                </div>

                <!-- Carousel Teks Alpine.js -->
                <div x-data="{ 
                        activeSlide: 0, 
                        slides: [
    { 
        title: 'Presisi Wajah AI', 
        desc: 'Verifikasi kehadiran guru lebih akurat dengan teknologi pemindaian biometrik wajah yang mencegah manipulasi data.' 
    },
    { 
        title: 'Keamanan Jaringan WiFi', 
        desc: 'Absensi hanya dapat dilakukan jika guru terhubung ke jaringan WiFi sekolah untuk memastikan kehadiran fisik di lokasi.' 
    },
    { 
        title: 'Manajemen Izin Terpadu', 
        desc: 'Pengajuan sakit, izin, atau cuti dapat dilakukan langsung dari aplikasi dan terintegrasi otomatis dengan laporan kehadiran.' 
    }
]
                    }" 
                    x-init="setInterval(() => { activeSlide = activeSlide === slides.length - 1 ? 0 : activeSlide + 1 }, 4000)"
                    class="text-center min-h-[100px]">
                    
                    <template x-for="(slide, index) in slides" :key="index">
                        <div x-show="activeSlide === index" 
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             style="display: none;">
                            <h3 class="text-xl font-bold text-white mb-2" x-text="slide.title"></h3>
                            <p class="text-white/80 text-xs leading-relaxed" x-text="slide.desc"></p>
                        </div>
                    </template>

                    <!-- Indikator Slide -->
                    <div class="flex justify-center space-x-2 mt-6">
                        <template x-for="(slide, index) in slides" :key="index">
                            <button @click="activeSlide = index" 
                                class="h-1.5 rounded-full transition-all duration-300"
                                :class="activeSlide === index ? 'w-6 bg-white' : 'w-2 bg-white/40 hover:bg-white/70'">
                            </button>
                        </template>
                    </div>
                </div>



            </div>
        </div>

    </div>

    <!-- MODAL LUPA PASSWORD -->
    <div x-show="showModal" 
         style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/40 backdrop-blur-sm p-4">
        
        <div class="fixed inset-0" @click="showModal = false"></div>

        <div x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative bg-white rounded-[2rem] shadow-2xl p-8 w-full max-w-sm text-center z-10">
            
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-[#F3F6FA] mb-6">
                <i class="fa-solid fa-shield-halved text-2xl text-[#002D8B]"></i>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 mb-2">Lupa Password?</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                Silakan menghubungi <span class="font-bold text-gray-800">Admin Sekolah</span> atau bagian IT untuk mengatur ulang kata sandi Anda.
            </p>
            
            <button @click="showModal = false" 
                class="w-full bg-[#002D8B] hover:bg-[#001A52] text-white font-bold py-3.5 rounded-2xl transition-colors duration-200 text-sm">
                Mengerti
            </button>
        </div>
    </div>

</div>
@endsection
