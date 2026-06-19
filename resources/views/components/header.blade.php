<header class="flex items-center justify-between h-16 px-6 bg-white shadow-sm border-b border-gray-100 z-10 relative shrink-0 font-poppins">
    
    <div class="flex items-center">
        <button @click="isSidebarOpen = !isSidebarOpen" class="text-gray-500 hover:text-[#002D8B] focus:outline-none lg:hidden transition-colors">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
        <button @click="isMini = !isMini" class="hidden text-gray-500 hover:text-[#002D8B] focus:outline-none lg:block transition-colors">
            <i class="fa-solid fa-bars-staggered text-xl"></i>
        </button>
        
        <h2 class="ml-4 text-xl font-bold text-gray-800 hidden sm:block">@yield('page_title', 'Dashboard')</h2>
    </div>

    <div class="flex items-center gap-4 md:gap-5">
        
        <div x-data="realtimeClock()" class="hidden md:flex flex-col text-right border-r border-gray-200 pr-4">
            <span class="text-sm font-bold text-[#002D8B]" x-text="time"></span>
            <span class="text-xs text-gray-500" x-text="date"></span>
        </div>

        @php
            $masterBypass = \App\Models\IpLokal::where('ip_address', '*')->first();
            $isBypassed = $masterBypass ? $masterBypass->is_active : false;
        @endphp

        <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 {{ $isBypassed ? 'bg-red-50 text-red-600 border-red-200' : 'bg-green-50 text-green-600 border-green-200' }} border rounded-full text-xs font-bold">
            <div class="w-2 h-2 {{ $isBypassed ? 'bg-red-500' : 'bg-green-500' }} rounded-full animate-pulse"></div>
            {{ $isBypassed ? 'Validasi WiFi: OFF' : 'Validasi WiFi: ON' }}
        </div>

        @php
            $notifications = \Illuminate\Support\Facades\DB::table('notifications')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            $unreadCount = $notifications->where('is_read', 0)->count();
        @endphp

        <div class="relative" x-data="{ notifOpen: false }" @click.away="notifOpen = false">
            <button @click="notifOpen = !notifOpen" class="relative text-gray-400 hover:text-[#002D8B] transition-colors focus:outline-none mt-1">
                <i class="fa-regular fa-bell text-xl"></i>
                @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 border border-white rounded-full animate-ping"></span>
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 border border-white rounded-full"></span>
                @endif
            </button>
            
            <div x-show="notifOpen" 
                 x-transition.opacity.duration.200ms
                 class="absolute right-0 top-full mt-3 w-80 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden z-50" style="display: none;">
                <div class="p-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-800">Notifikasi Sistem</span>
                    @if($unreadCount > 0)
                    <span class="text-[10px] bg-[#002D8B] text-white px-2 py-0.5 rounded-full font-bold tracking-wider">{{ $unreadCount }} BARU</span>
                    @endif
                </div>
                <div class="max-h-72 overflow-y-auto">
                    @forelse($notifications as $notif)
                    <div class="p-4 border-b border-gray-50 hover:bg-blue-50/50 transition flex gap-3 items-start cursor-pointer {{ $notif->is_read ? 'opacity-60' : '' }}">
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-[#002D8B] flex items-center justify-center shrink-0">
                            <i class="fa-solid {{ $notif->icon ?? 'fa-bell' }} text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-800">{!! $notif->message !!}</p>
                            <p class="text-[10px] text-gray-400 mt-1 font-medium"><i class="fa-regular fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-gray-500 text-xs">
                        Belum ada notifikasi
                    </div>
                    @endforelse
                </div>
                <div class="p-3 text-center bg-gray-50 hover:bg-gray-100 transition border-t border-gray-100">
                    <a href="{{ route('notifications.readAll') }}" class="text-xs font-bold text-[#002D8B]">Tandai Semua Dibaca</a>
                </div>
            </div>
        </div>

        <div class="relative" x-data="{ dropdownOpen: false }" @mouseenter="dropdownOpen = true" @mouseleave="dropdownOpen = false">
            <button class="flex items-center gap-3 focus:outline-none py-2">
                <div class="hidden md:block text-right">
                    <p class="text-sm font-bold text-gray-700 leading-tight">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', auth()->user()->role ?? 'Role') }}</p>
                </div>
                
                <img src="{{ auth()->user()->foto_profil ? asset('storage/' . auth()->user()->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name ?? 'User').'&background=002D8B&color=fff&bold=true' }}" 
                     alt="Avatar" class="w-10 h-10 rounded-xl shadow-sm object-cover border border-gray-200">
                     
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200" :class="{'rotate-180': dropdownOpen}"></i>
            </button>

            <div x-show="dropdownOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="absolute right-0 top-full mt-1 w-48 bg-white rounded-xl overflow-hidden shadow-lg z-50 border border-gray-100 py-1" 
                 style="display: none;">
                
                <a href="{{ route('profile.index') }}" class="block px-4 py-2.5 text-sm text-gray-600 font-medium hover:bg-gray-50 hover:text-[#002D8B] transition-colors">
                    <i class="fa-regular fa-user mr-2"></i> Profil Saya
                </a>
                
                <div class="border-t border-gray-100 my-1"></div>
                
                <form action="{{ route('logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm font-medium text-red-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Keluar Aplikasi
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>