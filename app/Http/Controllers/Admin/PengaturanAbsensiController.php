<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengaturanAbsensi;

class PengaturanAbsensiController extends Controller
{
    public function index()
    {
        // Ambil data pertama, jika belum ada buat defaultnya
        $pengaturan = PengaturanAbsensi::first() ?? new PengaturanAbsensi([
            'jam_buka_absen' => '06:00:00',
            'batas_jam_masuk' => '07:15:00',
            'jam_pulang' => '14:00:00',
            'denda_terlambat' => 10000
        ]);
        
        return view('admin.pengaturan_absensi.index', compact('pengaturan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jam_buka_absen' => 'required',
            'batas_jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'denda_terlambat' => 'required|numeric|min:0'
        ]);

        $pengaturan = PengaturanAbsensi::first();

        if ($pengaturan) {
            $pengaturan->update($request->all());
        } else {
            PengaturanAbsensi::create($request->all());
        }

        return redirect()->back()->with('success', 'Pengaturan Jam Kerja & Denda berhasil diperbarui!');
    }
}