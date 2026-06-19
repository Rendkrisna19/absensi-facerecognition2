<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Load relasi 'guru' agar data no_hp dari tabel gurus bisa terbaca di View
        $user->load('guru'); 
        
        return view('guru.pengaturan.index', compact('user'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'no_hp' => 'nullable|numeric|digits_between:10,15' // Validasi tambahan untuk Nomor HP
        ]);

        $user = auth()->user();

        // 1. Update Foto Profil (Tabel Users)
        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::exists('public/' . $user->foto_profil)) {
                Storage::delete('public/' . $user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profil', 'public');
            $user->update(['foto_profil' => $path]);
        }

        // 2. Update Nomor HP (Tabel Gurus / Biodata)
        if ($request->has('no_hp')) {
            $user->guru()->updateOrCreate(
                ['user_id' => $user->id],
                ['no_hp' => $request->no_hp]
            );
        }

        return redirect()->back()->with('success', 'Profil dan informasi kontak berhasil diperbarui!');
    }
}