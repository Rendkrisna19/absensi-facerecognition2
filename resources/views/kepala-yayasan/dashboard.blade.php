@extends('layouts.app')

@section('title', 'Dashboard Yayasan')
@section('page_title', 'Dashboard Eksekutif')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="space-y-6">
    
    <div class="bg-gradient-to-r from-[#002D8B] to-[#001f63] rounded-2xl shadow-lg p-6 md:p-8 text-white flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="mb-4 md:mb-0 relative z-5">
            <h2 class="text-2xl font-bold">Ringkasan Manajemen Tri Jaya</h2>
            <p class="text-blue-200 mt-1 text-sm md:text-base">Pantau kedisiplinan dan estimasi pemotongan gaji secara real-time.</p>
        </div>
        
        <form method="GET" class="relative z-10 flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 p-2.5 rounded-xl">
            <select name="bulan" class="bg-white/90 text-gray-800 text-sm font-bold rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300 border-none shadow-inner">
                @foreach(range(1, 12) as $bln)
                    <option value="{{ $bln }}" {{ $bulanSelected == $bln ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($bln)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="tahun" class="bg-white/90 text-gray-800 text-sm font-bold rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300 border-none shadow-inner">
                @foreach(range(date('Y')-2, date('Y')) as $thn)
                    <option value="{{ $thn }}" {{ $tahunSelected == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
            <select name="unit_sekolah" class="bg-white/90 text-gray-800 text-sm font-bold rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300 border-none shadow-inner">
                <option value="all" {{ $unitSelected == 'all' ? 'selected' : '' }}>Semua Unit</option>
                <option value="SD" {{ $unitSelected == 'SD' ? 'selected' : '' }}>Unit SD</option>
                <option value="SMP" {{ $unitSelected == 'SMP' ? 'selected' : '' }}>Unit SMP</option>
            </select>
            <button type="submit" class="bg-white text-[#002D8B] hover:bg-gray-100 px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
        </form>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border-t-4 border-blue-500 shadow-md hover:shadow-xl hover:-translate-y-1 relative overflow-hidden group transition-all duration-300">
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Total Guru</p>
            <h3 class="text-3xl font-black text-gray-800">{{ $metrics['total_guru'] }}</h3>
            <i class="fa-solid fa-users absolute -right-3 -bottom-3 text-6xl text-blue-50 group-hover:text-blue-100 group-hover:scale-110 transition-transform duration-300"></i>
        </div>
        <div class="bg-white rounded-2xl p-5 border-t-4 border-green-500 shadow-md hover:shadow-xl hover:-translate-y-1 relative overflow-hidden group transition-all duration-300">
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Hadir Tepat (Hari Ini)</p>
            <h3 class="text-3xl font-black text-green-600">{{ $metrics['hadir_tepat'] }}</h3>
            <i class="fa-solid fa-check-circle absolute -right-3 -bottom-3 text-6xl text-green-50 group-hover:text-green-100 group-hover:scale-110 transition-transform duration-300"></i>
        </div>
        <div class="bg-white rounded-2xl p-5 border-t-4 border-yellow-500 shadow-md hover:shadow-xl hover:-translate-y-1 relative overflow-hidden group transition-all duration-300">
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Terlambat (Hari Ini)</p>
            <h3 class="text-3xl font-black text-yellow-500">{{ $metrics['terlambat_hari_ini'] }}</h3>
            <i class="fa-solid fa-clock absolute -right-3 -bottom-3 text-6xl text-yellow-50 group-hover:text-yellow-100 group-hover:scale-110 transition-transform duration-300"></i>
        </div>
        <div class="bg-white rounded-2xl p-5 border-t-4 border-red-500 shadow-md hover:shadow-xl hover:-translate-y-1 relative overflow-hidden group transition-all duration-300">
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Belum Absen / Alpa</p>
            <h3 class="text-3xl font-black text-red-500">{{ $metrics['alpa_hari_ini'] }}</h3>
            <i class="fa-solid fa-user-xmark absolute -right-3 -bottom-3 text-6xl text-red-50 group-hover:text-red-100 group-hover:scale-110 transition-transform duration-300"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 lg:col-span-2 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50/50 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="flex justify-between items-center mb-6 relative z-10">
                <div>
                    <h4 class="text-lg font-black text-gray-800 tracking-tight">Tren Kehadiran Harian</h4>
                    <p class="text-xs text-gray-500 font-medium mt-1">Perbandingan tingkat kedisiplinan 7 hari terakhir</p>
                </div>
            </div>
            <div id="chart-tren" class="w-full h-[300px]"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-base font-bold text-gray-800">5 Absensi Terakhir Hari Ini</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100 uppercase text-[10px]">
                            <th class="pb-2 font-medium">Nama & NIK</th>
                            <th class="pb-2 font-medium text-right">Waktu</th>
                            <th class="pb-2 font-medium text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($absenTerakhir as $absen)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3">
                                <p class="font-semibold text-gray-800 text-xs">{{ $absen->user->name }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $absen->user->nik }}</p>
                            </td>
                            <td class="py-3 text-right font-mono text-xs text-gray-600 font-bold">
                                {{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') }}
                            </td>
                            <td class="py-3 text-right">
                                @if($absen->status == 'Terlambat')
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-[10px] font-bold border border-yellow-200">Telat</span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold border border-green-200">Tepat</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-400 text-xs">
                                <i class="fa-solid fa-folder-open text-2xl mb-2 text-gray-300 block"></i>
                                Belum ada data absensi hari ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col relative overflow-hidden">
            <h4 class="text-base font-black text-gray-800 mb-6 text-center z-10 relative tracking-tight">Distribusi Kehadiran</h4>
            <div id="chart-pie" class="flex justify-center flex-1 items-center z-10 relative mt-4"></div>
            <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-gray-50 to-transparent z-0"></div>
        </div>

        <div class="bg-gradient-to-br from-rose-50 to-white rounded-3xl shadow-sm border border-rose-100 p-8 flex flex-col justify-center relative overflow-hidden group">
            <div class="absolute right-0 bottom-0 mb-4 mr-4 text-rose-500/10 text-8xl group-hover:scale-110 group-hover:-rotate-12 transition-all duration-500">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div class="relative z-10 w-full">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                        <i class="fa-solid fa-arrow-trend-down text-sm"></i>
                    </div>
                    <p class="text-xs font-black text-rose-600 uppercase tracking-widest">Estimasi Potongan</p>
                </div>
                <h4 class="text-4xl font-black text-gray-800 mb-2 font-mono tracking-tighter">Rp {{ number_format($metrics['total_denda_bulan_ini'], 0, ',', '.') }}</h4>
                <p class="text-xs text-gray-500 font-medium">Akumulasi denda keterlambatan bulan <b>{{ $bulanSekarang }}</b></p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-white rounded-3xl shadow-sm border border-emerald-100 p-8 flex flex-col justify-center relative overflow-hidden group">
            <div class="absolute right-0 bottom-0 mb-4 mr-4 text-emerald-500/10 text-8xl group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">
                <i class="fa-solid fa-ranking-star"></i>
            </div>
            <div class="relative z-10 w-full">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="fa-solid fa-percent text-sm"></i>
                    </div>
                    <p class="text-xs font-black text-emerald-600 uppercase tracking-widest">Rata-rata Disiplin</p>
                </div>
                <div class="flex items-end gap-3 mb-3">
                    <h4 class="text-5xl font-black text-gray-800 font-mono tracking-tighter leading-none">{{ $metrics['rata_kehadiran'] }}<span class="text-2xl text-emerald-500">%</span></h4>
                </div>
                <div class="w-full bg-emerald-100/50 rounded-full h-2.5 mb-2 overflow-hidden shadow-inner">
                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2.5 rounded-full relative" style="width: {{ $metrics['rata_kehadiran'] }}%">
                        <div class="absolute inset-0 bg-white/20 w-full animate-pulse"></div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 font-medium">Target minimum yayasan: <span class="font-bold text-gray-700">90%</span></p>
            </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. BAR CHART (Tren Mingguan - Premium Style)
        var optionsTren = {
            series: [{
                name: 'Tepat Waktu',
                data: @json($chartData['hadir'])
            }, {
                name: 'Terlambat',
                data: @json($chartData['terlambat'])
            }, {
                name: 'Alpa/Izin',
                data: @json($chartData['alpa'])
            }],
            chart: {
                height: 320,
                type: 'bar',
                stacked: false,
                toolbar: { show: false },
                fontFamily: "'Poppins', sans-serif",
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            colors: ['#10b981', '#f59e0b', '#ef4444'], 
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 4,
                    dataLabels: { position: 'top' }
                },
            },
            dataLabels: { 
                enabled: true,
                offsetY: -20,
                style: { fontSize: '10px', colors: ['#6b7280'] },
                formatter: function (val) { return val > 0 ? val : ''; }
            },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: @json($chartData['labels']),
                labels: { style: { colors: '#6b7280', fontSize: '11px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#9ca3af', fontSize: '11px' } }
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } } },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px', fontWeight: 600, markers: { radius: 12 } },
            fill: { opacity: 1 },
            tooltip: { 
                theme: 'light',
                y: { formatter: function (val) { return val + " Pegawai" } }
            }
        };
        var chartTren = new ApexCharts(document.querySelector("#chart-tren"), optionsTren);
        chartTren.render();

        // 2. RADIAL BAR CHART (Komposisi Hari Ini - Premium Style)
        var totalHariIni = {{ $metrics['hadir_tepat'] + $metrics['terlambat_hari_ini'] + $metrics['alpa_hari_ini'] }};
        totalHariIni = totalHariIni === 0 ? 1 : totalHariIni; // Prevent div by zero
        
        var pctHadir = Math.round(({{ $metrics['hadir_tepat'] }} / totalHariIni) * 100);
        var pctTelat = Math.round(({{ $metrics['terlambat_hari_ini'] }} / totalHariIni) * 100);
        var pctAlpa = Math.round(({{ $metrics['alpa_hari_ini'] }} / totalHariIni) * 100);

        var optionsPie = {
            series: [pctHadir, pctTelat, pctAlpa],
            labels: ['Tepat Waktu', 'Terlambat', 'Belum Hadir'],
            chart: {
                type: 'radialBar',
                height: 300,
                fontFamily: "'Poppins', sans-serif"
            },
            colors: ['#10b981', '#f59e0b', '#ef4444'],
            plotOptions: {
                radialBar: {
                    hollow: { size: '45%', background: 'transparent' },
                    track: { background: '#f1f5f9', strokeWidth: '100%', margin: 5 },
                    dataLabels: {
                        name: { fontSize: '12px', fontWeight: 600, color: '#6b7280' },
                        value: { fontSize: '24px', fontWeight: 800, color: '#1f2937', formatter: function (val) { return val + "%" } },
                        total: { show: true, label: 'Disiplin', color: '#10b981', formatter: function (w) { return pctHadir + "%" } }
                    }
                }
            },
            stroke: { lineCap: 'round' },
            legend: { show: true, position: 'bottom', fontSize: '11px', fontWeight: 600, markers: { radius: 12 } }
        };
        var chartPie = new ApexCharts(document.querySelector("#chart-pie"), optionsPie);
        chartPie.render();

    });
</script>
@endsection