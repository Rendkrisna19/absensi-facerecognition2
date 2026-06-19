@extends('layouts.app')

@section('title', 'Potongan Gaji')
@section('page_title', 'Rekap Pemotongan Gaji (Keterlambatan & Ketidak-hadiran)')

@section('content')
<div class="space-y-6 print-area" x-data="{ modalOpen: false, modalData: null }">
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 no-print">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Laporan Potongan Gaji Guru</h2>
                <p class="text-sm text-gray-500">Berdasarkan akumulasi keterlambatan & ketidak-hadiran bulan: <strong class="text-[#002D8B]">{{ $namaBulanTahun }}</strong></p>
            </div>
            
            <div class="flex items-center gap-3">
                <form method="GET" class="flex flex-wrap md:flex-nowrap items-center gap-2 bg-gray-50 p-1.5 rounded-lg border border-gray-200 shadow-inner">
                    <select name="unit_sekolah" class="bg-transparent text-sm font-bold outline-none px-2 cursor-pointer text-gray-700">
                        <option value="Semua" {{ $unitSekolah == 'Semua' ? 'selected' : '' }}>Semua Unit</option>
                        <option value="SD" {{ $unitSekolah == 'SD' ? 'selected' : '' }}>SD</option>
                        <option value="SMP" {{ $unitSekolah == 'SMP' ? 'selected' : '' }}>SMP</option>
                    </select>
                    <div class="w-px h-4 bg-gray-300"></div>
                    <select name="bulan" class="bg-transparent text-sm font-bold outline-none px-2 cursor-pointer text-gray-700">
                        @foreach(range(1, 12) as $bln)
                            <option value="{{ $bln }}" {{ $bulanSelected == $bln ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($bln)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    <div class="w-px h-4 bg-gray-300"></div>
                    <select name="tahun" class="bg-transparent text-sm font-bold outline-none px-2 cursor-pointer text-gray-700">
                        @foreach(range(date('Y')-2, date('Y')) as $thn)
                            <option value="{{ $thn }}" {{ $tahunSelected == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-[#002D8B] hover:bg-[#001f63] text-white px-4 py-1.5 rounded-md text-xs font-bold transition-colors shadow-sm">
                        Filter Data
                    </button>
                </form>

               <div class="flex items-center gap-2">
    <a href="{{ route('yayasan.potongan.pdf', request()->all()) }}" target="_blank" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 hover:border-transparent px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-file-pdf"></i> Cetak PDF
    </a>
    
    <a href="{{ route('yayasan.potongan.excel', request()->all()) }}" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border border-emerald-200 hover:border-transparent px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-file-excel"></i> Export Excel
    </a>
</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl p-6 text-white shadow-md relative overflow-hidden">
            <i class="fa-solid fa-file-invoice-dollar absolute -right-4 -bottom-4 text-7xl text-white/10"></i>
            <p class="text-xs text-red-200 font-bold uppercase tracking-wider mb-1">Total Akumulasi Potongan</p>
            <h3 class="text-3xl font-black">Rp {{ number_format($totalKeseluruhanPotongan, 0, ',', '.') }}</h3>
            <p class="text-[10px] mt-2 text-red-100"><i class="fa-solid fa-circle-info mr-1"></i> Akan dipotong dari gaji yayasan bulan ini</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Jumlah Guru Terpotong</p>
            <h3 class="text-3xl font-black text-gray-800">{{ $totalGuruDipotong }} <span class="text-sm font-medium text-gray-400">Orang</span></h3>
            <p class="text-[10px] mt-2 text-gray-400">Guru yang terlambat atau tidak absen minimal 1 kali di bulan ini</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Aturan Denda Sistem</p>
            <h3 class="text-3xl font-black text-orange-500">Rp {{ number_format($nominalDenda, 0, ',', '.') }}</h3>
            <p class="text-[10px] mt-2 text-gray-400 font-bold">Dikalikan (x) jumlah hari terlambat + tidak absen</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 no-print">
            <div class="relative max-w-sm">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg block w-full pl-10 px-4 py-2 focus:ring-red-500 focus:border-red-500 outline-none" placeholder="Cari nama guru atau NIK...">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" id="reportTable">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 border-b border-gray-200 text-xs uppercase tracking-wider">
                        <th class="px-5 py-4 font-bold text-center w-12">No</th>
                        <th class="px-5 py-4 font-bold">Data Guru</th>
                        <th class="px-5 py-4 font-bold text-center">Frekuensi Telat</th>
                        <th class="px-5 py-4 font-bold text-center">Tidak Absen</th>
                        <th class="px-5 py-4 font-bold text-right">Nominal Potongan</th>
                        <th class="px-5 py-4 font-bold text-center no-print w-32">Rincian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dataPotongan as $index => $data)
                    <tr class="hover:bg-red-50/30 transition-colors guru-row {{ ($data->jumlah_telat + $data->jumlah_alpa) > 0 ? 'bg-white' : 'bg-gray-50/30 opacity-60' }}">
                        <td class="px-5 py-4 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-[#002D8B] flex items-center justify-center font-bold shrink-0 border border-blue-100 overflow-hidden no-print">
                                    @if($data->foto)
                                        <img src="{{ asset('storage/' . $data->foto) }}" alt="Profil" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user text-blue-300"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 guru-name text-sm">{{ $data->name }}</p>
                                    <p class="text-[10px] text-gray-500 font-mono guru-nik">{{ $data->nik }} • {{ $data->jabatan }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($data->jumlah_telat > 0)
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold border border-orange-200">
                                    {{ $data->jumlah_telat }} Kali
                                </span>
                            @else
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                                    0
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($data->jumlah_alpa > 0)
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold border border-red-200">
                                    {{ $data->jumlah_alpa }} Kali
                                </span>
                            @else
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                                    0
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-black text-base {{ $data->total_potongan > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                Rp {{ number_format($data->total_potongan, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center no-print">
                            @if(($data->jumlah_telat + $data->jumlah_alpa) > 0)
                                <button @click="modalData = {{ json_encode([
                                    'name' => $data->name,
                                    'potongan' => number_format($data->total_potongan, 0, ',', '.'),
                                    'riwayat' => $data->riwayat->map(function($r) {
                                        return [
                                            'tanggal' => \Carbon\Carbon::parse($r->tanggal)->translatedFormat('l, d F Y'),
                                            'jam' => $r->jam_masuk ? \Carbon\Carbon::parse($r->jam_masuk)->format('H:i') : null,
                                            'menit' => $r->menit_terlambat,
                                            'status' => $r->status
                                        ];
                                    })
                                ]) }}; modalOpen = true" 
                                class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-[#002D8B] hover:text-white flex items-center justify-center transition-all shadow-sm tooltip" title="Lihat Rincian">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>
                            @else
                                <span class="text-gray-300 text-xs"><i class="fa-solid fa-minus"></i></span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                            <i class="fa-solid fa-folder-open text-4xl mb-3 block text-gray-300"></i>
                            Tidak ada data guru untuk ditampilkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($dataPotongan) > 0)
                <tfoot class="bg-gray-100 font-bold border-t-2 border-gray-300 text-gray-800">
                    <tr>
                        <td colspan="4" class="px-5 py-4 text-right uppercase tracking-wider text-xs">Total Potongan Seluruh Guru Bulan Ini</td>
                        <td class="px-5 py-4 text-right text-red-600 text-lg">Rp {{ number_format($totalKeseluruhanPotongan, 0, ',', '.') }}</td>
                        <td class="no-print"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto no-print" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" aria-hidden="true" @click="modalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <div class="bg-[#002D8B] px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-white" id="modal-title">
                        <i class="fa-solid fa-clock-rotate-left mr-2"></i> Rincian Keterlambatan & Ketidak-hadiran
                    </h3>
                    <button @click="modalOpen = false" class="text-blue-200 hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <div class="bg-white px-6 pt-5 pb-6">
                    <template x-if="modalData">
                        <div>
                            <div class="flex justify-between items-end border-b border-gray-100 pb-3 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Nama Guru</p>
                                    <p class="text-lg font-bold text-gray-800" x-text="modalData.name"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-red-400 font-bold uppercase tracking-wider mb-1">Total Potongan</p>
                                    <p class="text-xl font-black text-red-600">Rp <span x-text="modalData.potongan"></span></p>
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 font-bold mb-3"><i class="fa-solid fa-calendar-days mr-1"></i> Daftar Tanggal:</p>
                            
                            <div class="max-h-64 overflow-y-auto pr-2 custom-scrollbar space-y-2">
                                <template x-for="(item, index) in modalData.riwayat" :key="index">
                                    <div class="flex justify-between items-center p-3 rounded-xl"
                                         :class="item.status === 'Terlambat' ? 'bg-orange-50/50 border border-orange-100' : 'bg-gray-50 border border-gray-200'">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                                                 :class="item.status === 'Terlambat' ? 'bg-orange-100 text-orange-500' : 'bg-gray-200 text-gray-500'">
                                                <i :class="item.status === 'Terlambat' ? 'fa-solid fa-exclamation' : 'fa-solid fa-circle-xmark'"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800" x-text="item.tanggal"></p>
                                                <template x-if="item.jam">
                                                    <p class="text-xs text-orange-500 font-medium">Masuk: <span x-text="item.jam"></span> WIB</p>
                                                </template>
                                                <template x-if="!item.jam">
                                                    <p class="text-xs text-gray-500 font-medium">Tidak absen</p>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <template x-if="item.status === 'Terlambat'">
                                                <span class="bg-white text-orange-600 text-[10px] font-bold px-2 py-1 rounded-md border border-orange-200 shadow-sm">
                                                    Telat <span x-text="item.menit"></span> Menit
                                                </span>
                                            </template>
                                            <template x-if="item.status === 'Alpa'">
                                                <span class="bg-white text-gray-600 text-[10px] font-bold px-2 py-1 rounded-md border border-gray-200 shadow-sm">
                                                    Tidak Absen
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="bg-gray-50 px-6 py-3 flex justify-end">
                    <button type="button" @click="modalOpen = false" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                        Tutup Rincian
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fca5a5; border-radius: 10px; }
    
    @media print {
        body { background-color: white !important; }
        .no-print, nav, aside, footer { display: none !important; }
        .print-area { padding: 0 !important; margin: 0 !important; }
        .shadow-sm, .shadow-md { box-shadow: none !important; }
        .rounded-2xl, .rounded-lg { border-radius: 0 !important; }
        .border-gray-100 { border-color: #e5e7eb !important; }
        * { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; }
        table { width: 100% !important; border-collapse: collapse !important; }
        th, td { border: 1px solid #e5e7eb !important; padding: 8px !important; }
        
        .print-area::before {
            content: "YAYASAN TRI JAYA - REKAP PEMOTONGAN GAJI (KETERLAMBATAN & KETIDAK-HADIRAN) BULAN {{ strtoupper($namaBulanTahun) }}";
            display: block;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #000;
        }
    }
</style>

<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.guru-row');
        
        rows.forEach(row => {
            const name = row.querySelector('.guru-name').textContent.toLowerCase();
            const nik = row.querySelector('.guru-nik').textContent.toLowerCase();
            
            if(name.includes(searchTerm) || nik.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection