<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanIzinController extends Controller
{
    /**
     * Menampilkan daftar pengajuan izin guru (Riwayat Pengajuan)
     */
    public function index()
    {
        $pengajuanIzins = PengajuanIzin::where('user_id', auth()->id())
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
                            
        return view('guru.pengajuan_izin.index', compact('pengajuanIzins'));
    }

    /**
     * Menampilkan form untuk mengajukan izin baru
     */
    public function create()
    {
        return view('guru.pengajuan_izin.create');
    }

    /**
     * Menyimpan data pengajuan izin ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:Sakit,Izin,Cuti',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:1000',
            'file_bukti' => $request->jenis == 'Sakit' ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'file_bukti.required' => 'Dokumen bukti wajib diunggah untuk pengajuan Sakit.',
        ]);

        $pathBukti = null;
        if ($request->hasFile('file_bukti')) {
            // Simpan di folder storage/app/public/bukti_izin agar aman
            $pathBukti = $request->file('file_bukti')->store('bukti_izin', 'public');
        }

        $izin = PengajuanIzin::create([
            'user_id' => auth()->id(),
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'file_bukti' => $pathBukti,
            'status' => 'Pending', // Default status
        ]);

        // Tambahkan notifikasi
        \Illuminate\Support\Facades\DB::table('notifications')->insert([
            'user_id' => auth()->id(), // user yang mengajukan (atau null jika global)
            'title' => 'Pengajuan Izin',
            'message' => '<b>' . auth()->user()->name . '</b> mengajukan ' . $request->jenis,
            'icon' => 'fa-envelope-open-text',
            'is_read' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('guru.pengajuan-izin.index')
                         ->with('success', 'Pengajuan ' . $request->jenis . ' berhasil dikirim dan menunggu persetujuan.');
    }

    /**
     * Menampilkan detail satu pengajuan (Opsional jika ingin dibuatkan view khusus)
     */
    public function show(PengajuanIzin $pengajuanIzin)
    {
        // Pastikan hanya bisa melihat miliknya sendiri
        abort_if($pengajuanIzin->user_id !== auth()->id(), 403);
        
        return view('guru.pengajuan_izin.show', compact('pengajuanIzin'));
    }
    
    // Fitur Edit & Destroy bisa Anda biarkan kosong jika aturan sekolah 
    // tidak mengizinkan edit/hapus setelah pengajuan dikirim.
}