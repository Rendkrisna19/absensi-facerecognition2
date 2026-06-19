<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\CarbonPeriod;

class PengajuanIzinController extends Controller
{
    public function index()
    {
        // Mengambil semua pengajuan izin beserta data user (guru/pegawai)
        $pengajuanIzins = PengajuanIzin::with('user')->orderBy('created_at', 'desc')->get();

        // Menghitung statistik untuk Cards
        $totalPending = $pengajuanIzins->where('status', 'Pending')->count();
        $totalDisetujui = $pengajuanIzins->where('status', 'Disetujui')->count();
        $totalDitolak = $pengajuanIzins->where('status', 'Ditolak')->count();

        return view('admin.pengajuan_izin.index', compact('pengajuanIzins', 'totalPending', 'totalDisetujui', 'totalDitolak'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'catatan_penolakan' => 'nullable|string'
        ]);

        $izin = PengajuanIzin::findOrFail($id);
        $izin->status = $request->status;
        $izin->catatan_penolakan = $request->status === 'Ditolak' ? $request->catatan_penolakan : null;
        $izin->disetujui_oleh = auth()->id();
        $izin->direspon_pada = now();
        $izin->save();

        // LOGIKA PENTING: Jika disetujui, otomatis generate status absen (Sakit/Izin/Cuti) ke tabel absensis
        if ($request->status === 'Disetujui') {
            $period = CarbonPeriod::create($izin->tanggal_mulai, $izin->tanggal_selesai);
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                
                // Gunakan firstOrCreate agar jika tanggal tersebut sudah ada data (misal alpa), tidak duplikat,
                // Namun jika Anda ingin menimpa data Alpa menjadi Sakit, gunakan updateOrCreate:
                Absensi::updateOrCreate(
                    ['user_id' => $izin->user_id, 'tanggal' => $dateStr],
                    [
                        'status' => $izin->jenis, // 'Sakit', 'Izin', atau 'Cuti'
                        'jam_masuk' => null,
                        'menit_terlambat' => 0
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Status pengajuan izin berhasil diperbarui!');
    }

    public function cleanup(Request $request)
    {
        $request->validate([
            'tipe_hapus' => 'required|in:hari,minggu,semua',
            'tanggal' => 'required_if:tipe_hapus,hari|date',
            'minggu' => 'required_if:tipe_hapus,minggu', 
        ]);

        try {
            $query = PengajuanIzin::query();
            $pesan = '';

            if ($request->tipe_hapus == 'hari') {
                $query->where('tanggal_mulai', '<=', $request->tanggal)
                      ->where('tanggal_selesai', '>=', $request->tanggal);
                $pesan = 'Data izin yang bersinggungan dengan tanggal ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y') . ' berhasil dihapus.';
            } elseif ($request->tipe_hapus == 'minggu') {
                $parts = explode('-W', $request->minggu);
                if(count($parts) == 2) {
                    $year = $parts[0];
                    $week = $parts[1];
                    $startOfWeek = \Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek();
                    $endOfWeek = $startOfWeek->copy()->endOfWeek();
                    $query->whereBetween('tanggal_mulai', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')]);
                    $pesan = 'Data izin minggu ke-' . $week . ' tahun ' . $year . ' berhasil dihapus.';
                } else {
                    return back()->with('error', 'Format minggu tidak valid.');
                }
            } elseif ($request->tipe_hapus == 'semua') {
                $pesan = 'Semua data izin berhasil dibersihkan.';
            }

            // Hapus file bukti fisik sebelum record di database dihapus
            $izins = $query->get();
            foreach($izins as $izin) {
                if($izin->file_bukti && \Illuminate\Support\Facades\Storage::disk('public')->exists($izin->file_bukti)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($izin->file_bukti);
                }
            }

            $deleted = $query->delete();
            return back()->with('success', $pesan . ' (' . $deleted . ' baris terhapus)');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan data: ' . $e->getMessage());
        }
    }
}