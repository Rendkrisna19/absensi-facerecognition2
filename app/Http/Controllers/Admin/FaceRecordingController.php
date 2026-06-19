<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FaceRecordingController extends Controller
{
    // Menampilkan daftar guru yang bisa direkam wajahnya
    public function index(Request $request)
    {
        // Query dasar: Ambil data khusus guru
        $baseQuery = User::where('role', 'guru')->latest();

        // 1. Hitung Statistik untuk Card (gunakan clone agar query utama tidak terpengaruh)
        $totalGuru = (clone $baseQuery)->count();
        $sudahRekam = (clone $baseQuery)->whereNotNull('face_descriptor')->count();
        $belumRekam = (clone $baseQuery)->whereNull('face_descriptor')->count();

        // Mulai query untuk tabel
        $query = User::where('role', 'guru')->latest();

        // 2. Filter berdasarkan Status Wajah
        if ($request->filled('status')) {
            if ($request->status == 'sudah') {
                $query->whereNotNull('face_descriptor');
            } elseif ($request->status == 'belum') {
                $query->whereNull('face_descriptor');
            }
        }

        // 3. Filter berdasarkan Pencarian (Nama / NIK)
        // Catatan: Jika di database Anda NIK disimpan di kolom 'username', ganti 'nik' menjadi 'username' di bawah ini
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%"); 
            });
        }

        // 4. Pagination
        $perPage = $request->input('per_page', 10);
        $gurus = $query->paginate($perPage)->withQueryString();

        return view('admin.face.index', compact('gurus', 'totalGuru', 'sudahRekam', 'belumRekam'));
    }

    public function record(User $guru)
    {
        if ($guru->role !== 'guru') {
            abort(404, 'Data bukan guru.');
        }


        return view('admin.face.record', compact('guru'));
    }
    // Menyimpan data array titik wajah (descriptor) ke database via AJAX
    public function store(Request $request, User $guru)
    {


        $request->validate([
            'face_descriptor' => 'required|string'
        ]);

        try {
            $guru->update([
                'face_descriptor' => $request->face_descriptor
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wajah berhasil direkam dan disimpan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan wajah: ' . $e->getMessage()
            ], 500);
        }
    }
}