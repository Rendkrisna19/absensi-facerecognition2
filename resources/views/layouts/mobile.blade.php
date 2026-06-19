<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title') - Tri Jaya</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #e5e7eb; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="flex justify-center min-h-screen">

    <div class="w-full max-w-[414px] bg-gray-50 min-h-screen relative shadow-2xl flex flex-col overflow-hidden">
        
        <header class="pt-8 pb-4 px-6 bg-white shrink-0 rounded-b-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] z-10">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 font-medium">@yield('subtitle', 'Selamat Datang,')</p>
                    <h1 class="text-lg font-bold text-gray-800">@yield('page_title', auth()->user()->name)</h1>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#002D8B]/10 text-[#002D8B] flex items-center justify-center font-bold border border-[#002D8B]/20 overflow-hidden shadow-sm">
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="Profil" class="w-full h-full object-cover">
                    @else
                        {{ substr(auth()->user()->name, 0, 1) }}
                    @endif
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto no-scrollbar p-6 pb-28">
            @yield('content')
        </main>

        <div class="absolute bottom-0 w-full px-3 pb-4 z-20 pointer-events-none">
            <div class="bg-white rounded-3xl shadow-[0_-4px_25px_rgba(0,0,0,0.08)] px-2 py-2.5 flex items-center relative pointer-events-auto border border-gray-100">
                
                <div class="flex w-1/2 pr-7">
                    <a href="{{ route('guru.dashboard') }}" class="flex-1 flex flex-col items-center justify-center text-[10px] sm:text-xs {{ request()->routeIs('guru.dashboard') ? 'text-[#002D8B] font-bold' : 'text-gray-400 font-medium' }}">
                        <i class="fa-solid fa-house text-lg mb-1 {{ request()->routeIs('guru.dashboard') ? 'mb-0.5' : '' }}"></i>
                        <span>Beranda</span>
                    </a>
                    
                    <a href="{{ route('guru.riwayat') }}" class="flex-1 flex flex-col items-center justify-center text-[10px] sm:text-xs {{ request()->routeIs('guru.riwayat') ? 'text-[#002D8B] font-bold' : 'text-gray-400 font-medium' }}">
                        <i class="fa-solid fa-clock-rotate-left text-lg mb-1 {{ request()->routeIs('guru.riwayat') ? 'mb-0.5' : '' }}"></i>
                        <span>Riwayat</span>
                    </a>
                </div>

                <div class="absolute left-1/2 -translate-x-1/2 -top-6 z-30">
                    <a href="{{ route('guru.scan') }}" class="flex items-center justify-center w-14 h-14 bg-[#002D8B] text-white rounded-full shadow-[0_8px_20px_rgba(0,45,139,0.4)] hover:scale-105 hover:bg-[#001f63] transition-transform border-4 border-white">
                        <i class="fa-solid fa-camera text-2xl"></i>
                    </a>
                </div>

                <div class="flex w-1/2 pl-7">
                    <a href="{{ route('guru.pengajuan-izin.index') }}" class="flex-1 flex flex-col items-center justify-center text-[10px] sm:text-[11px] {{ request()->routeIs('guru.pengajuan-izin.*') ? 'text-[#002D8B] font-bold' : 'text-gray-400 font-medium' }}">
                        <i class="fa-solid fa-envelope-open-text text-lg mb-1 {{ request()->routeIs('guru.pengajuan-izin.*') ? 'mb-0.5' : '' }}"></i>
                        <span>Izin</span>
                    </a>

                    <a href="{{ route('guru.denda') }}" class="flex-1 flex flex-col items-center justify-center text-[10px] sm:text-[11px] {{ request()->routeIs('guru.denda') ? 'text-[#002D8B] font-bold' : 'text-gray-400 font-medium' }}">
                        <i class="fa-solid fa-money-bill-wave text-lg mb-1 {{ request()->routeIs('guru.denda') ? 'mb-0.5' : '' }}"></i>
                        <span>Denda</span>
                    </a>

                    <a href="{{ route('guru.pengaturan') }}" class="flex-1 flex flex-col items-center justify-center text-[10px] sm:text-[11px] {{ request()->routeIs('guru.pengaturan') ? 'text-[#002D8B] font-bold' : 'text-gray-400 font-medium' }}">
                        <i class="fa-solid fa-gear text-lg mb-1 {{ request()->routeIs('guru.pengaturan') ? 'mb-0.5' : '' }}"></i>
                        <span>Akun</span>
                    </a>
                </div>

            </div>
        </div>

    </div>

    @stack('scripts')

    @if(session('toast_success'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: false,
            background: '#002D8B',
            color: '#fff',
            iconColor: '#fff',
            customClass: {
                popup: 'rounded-2xl',
                title: 'font-medium text-sm'
            }
        });
        Toast.fire({
            icon: 'success',
            title: "{{ session('toast_success') }}"
        });
    </script>
    @endif
</body>
</html>
