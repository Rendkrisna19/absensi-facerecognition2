<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        // Filter berdasarkan Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan Pencarian (Nama / Username)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Pagination dinamis (default 10 data per halaman)
        $perPage = $request->input('per_page', 10);
        
        // withQueryString() berguna agar saat pindah halaman (page 2), filter pencariannya tidak hilang
        $users = $query->paginate($perPage)->withQueryString(); 

        return view('admin.user.index', compact('users'));
    }

    // ---> TAMBAHAN: Method create untuk menampilkan form tambah pengguna <---
    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|size:16|unique:users,username', // Pastikan username unik dan 16 karakter
            'password' => 'required|min:6',
            'role' => 'required|in:admin,guru,kepala_yayasan', // Sesuaikan dengan role yang kamu punya
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'nik' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'Akun pengguna berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|size:16|unique:users,username,' . $user->id,
            'password' => 'nullable|min:6', // Boleh kosong jika tidak mau ganti password
            'role' => 'required|in:admin,guru,kepala_yayasan',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'nik' => $request->username,
            'role' => $request->role,
        ];

        // Jika password diisi, maka update passwordnya
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'Akun pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Proteksi: Admin tidak boleh menghapus akunnya sendiri saat sedang login
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login!');
        }

        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'Akun pengguna berhasil dihapus!');
    }
}