<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Tambahkan DB facade untuk notifikasi
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input wajib diisi
        $request->validate([
            'nik' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // 1. Cari user berdasarkan NIK
        $user = User::where('nik', $request->nik)->first();

        if (!$user) {
            // NIK tidak ditemukan dalam database
            return back()->withErrors([
                'nik' => 'NIK tidak terdaftar di sistem.',
            ])->onlyInput('nik');
        }

        // 2. Jika NIK ada, cek kecocokan password
        if (!Hash::check($request->password, $user->password)) {
            // Password salah
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ])->onlyInput('nik');
        }

        // 3. Jika NIK dan Password benar, proses login dengan REMEMBER ME
        // Parameter 'true' di bawah ini adalah kunci agar sesi tersimpan lama (Remember Token)
        // Jika di form login Anda ada checkbox "Ingat Saya" (name="remember"), gunakan: $request->has('remember')
        $remember = true; // Saya set true permanen agar selalu tersimpan sesuai permintaan Anda
        
        Auth::login($user, $remember);
        $request->session()->regenerate();
        
        // 4. Catat Aktivitas Login ke Tabel Notifikasi
        // Pastikan Anda sudah membuat tabel 'notifications' di database Anda
        try {
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'title'   => 'Login Baru',
                'message' => '<span class="text-[#002D8B] font-bold">' . $user->name . '</span> baru saja login ke dalam sistem.',
                'icon'    => 'fa-right-to-bracket',
                'is_read' => 0, // 0 = belum dibaca
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            // Abaikan error jika tabel notifications belum dibuat agar tidak mengganggu proses login
        }

        return $this->redirectUser($user->role, 'Berhasil Login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('toast_success', 'Berhasil Logout');
    }

    private function redirectUser($role, $message = null)
    {
        $redirect = null;
        switch ($role) {
            case 'admin':
                $redirect = redirect()->intended('/admin/dashboard'); break;
            case 'kepala_yayasan':
                $redirect = redirect()->intended('/yayasan/dashboard'); break;
            case 'guru':
                $redirect = redirect()->intended('/guru/dashboard'); break;
            default:
                $redirect = redirect('/');
        }
        
        if ($message) {
            return $redirect->with('toast_success', $message);
        }
        return $redirect;
    }
}