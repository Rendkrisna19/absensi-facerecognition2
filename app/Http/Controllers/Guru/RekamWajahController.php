<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekamWajahController extends Controller
{
    /**
     * Tampilkan halaman rekam wajah mandiri untuk guru.
     * Hanya bisa diakses jika wajah BELUM terdaftar.
     */
    public function index()
    {
        $user = Auth::user();

        // Jika wajah sudah terdaftar, tolak akses
        if (!empty($user->face_descriptor)) {
            return redirect()->route('guru.dashboard')
                ->with('toast_error', 'Wajah Anda sudah terdaftar dan tidak dapat diubah.');
        }

        return view('guru.rekam-wajah.index', compact('user'));
    }

    /**
     * Simpan data wajah guru (hanya 1x, tidak bisa di-edit).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Keamanan: tolak jika wajah sudah ada
        if (!empty($user->face_descriptor)) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah sudah terdaftar sebelumnya. Tidak dapat diubah.'
            ], 403);
        }

        $request->validate([
            'face_descriptor' => 'required|string'
        ]);

        try {
            $user->update([
                'face_descriptor' => $request->face_descriptor
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wajah berhasil direkam! Anda sudah bisa melakukan absensi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan wajah: ' . $e->getMessage()
            ], 500);
        }
    }
}
