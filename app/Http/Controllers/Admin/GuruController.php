<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guru;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Exports\GuruExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = User::with('guru')
            ->where('role', 'guru')
            ->orderBy('name', 'asc') 
            ->get();

        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|numeric|unique:users,nik',
            'password' => 'required|min:6',
            'jabatan' => 'nullable|in:guru,kepala_sekolah', 
            'unit_sekolah' => 'nullable|array',
            'unit_sekolah.*' => 'in:SD,SMP',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp' => 'nullable|numeric',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $fotoPath = null;
            if ($request->hasFile('foto_profil')) {
                $fotoPath = $request->file('foto_profil')->store('profil', 'public');
            }

            DB::transaction(function () use ($request, $fotoPath) {
                // 1. Simpan ke tabel Users
                $user = User::create([
                    'name' => $request->name,
                    'username' => $request->nik, 
                    'nik' => $request->nik,
                    'password' => Hash::make($request->password),
                    'role' => 'guru',
                    'jabatan' => $request->jabatan ?? 'guru',
                    'unit_sekolah' => is_array($request->unit_sekolah) ? implode(', ', $request->unit_sekolah) : null,
                    'foto_profil' => $fotoPath, 
                ]);

                
                Guru::create([
                    'user_id' => $user->id,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'agama' => $request->agama,
                    'alamat' => $request->alamat,
                    'no_hp' => $request->no_hp,
                    'pendidikan_terakhir' => $request->pendidikan_terakhir,
                    'tanggal_bergabung' => $request->tanggal_bergabung,
                ]);
            });

            return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(User $guru)
    {
        if ($guru->role !== 'guru') {
            abort(404, 'Data guru tidak ditemukan');
        }

        $guru->load('guru');

        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, User $guru)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|numeric|unique:users,nik,' . $guru->id,
            'password' => 'nullable|min:6', 
            'jabatan' => 'nullable|in:guru,kepala_sekolah', 
            'unit_sekolah' => 'nullable|array',
            'unit_sekolah.*' => 'in:SD,SMP',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp' => 'nullable|numeric',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' 
        ]);

        try {
            DB::transaction(function () use ($request, $guru) {
                $userData = [
                    'name' => $request->name,
                    'username' => $request->nik, 
                    'nik' => $request->nik,
                    'jabatan' => $request->jabatan ?? $guru->jabatan,
                    'unit_sekolah' => is_array($request->unit_sekolah) ? implode(', ', $request->unit_sekolah) : null,
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                if ($request->hasFile('foto_profil')) {
                    if ($guru->foto_profil && Storage::disk('public')->exists($guru->foto_profil)) {
                        Storage::disk('public')->delete($guru->foto_profil);
                    }
                    $userData['foto_profil'] = $request->file('foto_profil')->store('profil', 'public');
                }

                $guru->update($userData);

              
                Guru::updateOrCreate(
                    ['user_id' => $guru->id],
                    [
                        'tempat_lahir' => $request->tempat_lahir,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'agama' => $request->agama,
                        'alamat' => $request->alamat,
                        'no_hp' => $request->no_hp,
                        'pendidikan_terakhir' => $request->pendidikan_terakhir,
                        'tanggal_bergabung' => $request->tanggal_bergabung,
                    ]
                );
            });

            return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(User $guru)
    {
        try {
            if ($guru->foto_profil && Storage::disk('public')->exists($guru->foto_profil)) {
                Storage::disk('public')->delete($guru->foto_profil);
            }

            $guru->delete(); // Karena cascade, biodata di tabel gurus otomatis terhapus
            return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new \App\Imports\GuruImport, $request->file('file'));
            return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\GuruTemplateExport, 'Template_Import_Guru.xlsx');
    }

    public function exportExcel()
    {
        return Excel::download(new GuruExport, 'Data_Pegawai_Tri_Jaya.xlsx');
    }

    public function exportPdf()
    {
        $gurus = User::with('guru')->where('role', 'guru')->orderBy('name', 'asc')->get();
        $pdf = Pdf::loadView('admin.guru.pdf', compact('gurus'))->setPaper('a4', 'landscape');
        return $pdf->download('Data_Pegawai_Tri_Jaya.pdf');
    }

    public function print(User $guru)
    {
        if ($guru->role !== 'guru') {
            abort(404, 'Data tidak ditemukan');
        }

        $guru->load('guru');

        $pdf = Pdf::loadView('admin.guru.print', compact('guru'))->setPaper('a4', 'portrait');
        return $pdf->stream('Profil_Pegawai_' . $guru->name . '.pdf');
    }
}
