@extends('layouts.app')

@section('title', 'Tambah LAN')
@section('page_title', 'Tambah Jaringan LAN')

@push('styles')
<style>
    .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 font-poppins">
    
    <div class="mb-8 border-b border-gray-100 pb-4">
        <h4 class="text-xl font-bold text-gray-800">Tambah Jaringan LAN Baru</h4>
        <p class="text-sm text-gray-500 mt-1">Tambahkan IP Address WiFi sekolah yang diizinkan untuk melakukan absensi.</p>
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

    <form action="{{ route('admin.pengaturan-lan.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Jaringan / Lokasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_jaringan" value="{{ old('nama_jaringan') }}" placeholder="Contoh: WiFi Ruang Guru Utama" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all placeholder-gray-400">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Alamat IP (IP Address) <span class="text-red-500">*</span>
                </label>
                
                <div class="flex flex-col gap-2">
                    <div class="flex">
                        <input type="text" id="ip_address" name="ip_address" value="{{ old('ip_address') }}" placeholder="Contoh: 103.144.xxx.xxx atau 192.168.1.%" required class="w-full font-mono border border-gray-300 rounded-l-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all placeholder-gray-400 text-blue-700">
                        <button type="button" onclick="detectMyIp()" class="bg-[#1e3b8b] hover:bg-[#152b69] text-white px-4 py-3 rounded-r-xl transition-colors text-sm font-semibold whitespace-nowrap flex items-center gap-2">
                            <i class="fa-solid fa-satellite-dish"></i> Deteksi IP
                        </button>
                    </div>

                    <div class="flex gap-2 mt-1">
                        <button type="button" onclick="document.getElementById('ip_address').value = '{{ $myIp }}'" class="text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg border border-gray-200 transition-colors">
                            Gunakan IP Perangkat Ini ({{ $myIp }})
                        </button>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                        <i class="fa-solid fa-circle-info mr-1 text-blue-500"></i> Jika aplikasi di-hosting online, deteksi IP di atas akan mengisi IP Publik sekolah. Jika aplikasi dijalankan offline (localhost), masukkan IP dengan tanda persen, contoh: <strong>192.168.1.%</strong> agar semua HP dengan WiFi yang sama bisa absen.
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status Jaringan</label>
                <div class="relative">
                    <select name="is_active" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all appearance-none bg-white">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif (Diizinkan Absen)</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non-Aktif (Diblokir)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                        <i class="fa-solid fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan Tambahan <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all placeholder-gray-400" placeholder="Tuliskan catatan khusus terkait jaringan ini jika ada...">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        <div class="flex flex-col-reverse md:flex-row justify-end gap-3 pt-6 mt-8 border-t border-gray-100">
            <a href="{{ route('admin.pengaturan-lan.index') }}" class="w-full md:w-auto px-6 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-colors text-sm text-center">
                Kembali
            </a>
            <button type="submit" class="w-full md:w-auto px-6 py-2.5 rounded-xl bg-[#1e3b8b] hover:bg-[#152b69] text-white font-semibold transition-colors shadow-sm flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-save"></i> Simpan Data
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Fungsi untuk mendeteksi IP Publik jaringan (Sangat akurat untuk WiFi sekolah)
    async function detectMyIp() {
        const ipInput = document.getElementById('ip_address');
        const oldVal = ipInput.value;
        
        ipInput.value = "Mendeteksi...";
        
        try {
            // Memanggil API Publik untuk mendapatkan IP Router
            const response = await fetch('https://api.ipify.org?format=json');
            const data = await response.json();
            ipInput.value = data.ip;
            
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'IP Publik Jaringan Berhasil Dideteksi!',
                showConfirmButton: false,
                timer: 3000
            });
        } catch (error) {
            ipInput.value = oldVal;
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mendeteksi',
                text: 'Pastikan Anda terkoneksi ke internet saat menekan tombol ini.',
            });
        }
    }
</script>
@endpush
@endsection