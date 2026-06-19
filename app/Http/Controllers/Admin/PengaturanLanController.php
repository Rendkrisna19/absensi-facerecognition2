<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpLokal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PengaturanLanController extends Controller
{
    public function index(Request $request)
    {
        // Deteksi IP server saat ini
        $serverIp = $request->server('SERVER_ADDR') ?: getHostByName(getHostName());
        if (!$serverIp || $serverIp === '127.0.0.1' || $serverIp === '::1') {
            $host = $request->getHost();
            if (filter_var($host, FILTER_VALIDATE_IP)) {
                $serverIp = $host;
            } else {
                $serverIp = getHostByName(getHostName());
            }
        }

        // Cek apakah IP server cocok dengan jaringan terdaftar
        $ipServerCocok = false;
        $jaringanCocok = '';

        $query = IpLokal::where('ip_address', '!=', '*')->latest();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_jaringan', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $perPage = $request->input('per_page', 10);
        $ips = $query->paginate($perPage)->withQueryString();

        // Cek kecocokan IP server dengan jaringan terdaftar
        $jaringanAktif = IpLokal::where('is_active', true)->where('ip_address', '!=', '*')->get();
        foreach ($jaringanAktif as $jaringan) {
            $allowedIp = $jaringan->ip_address;
            $pattern = str_replace('%', '*', $allowedIp);
            if (Str::is($pattern, $serverIp)) {
                $ipServerCocok = true;
                $jaringanCocok = $jaringan->nama_jaringan;
                break;
            }
            $partsAllowed = explode('.', $allowedIp);
            $partsServer = explode('.', $serverIp);
            if (count($partsAllowed) === 4 && count($partsServer) === 4) {
                if ($partsAllowed[0] === $partsServer[0] && $partsAllowed[1] === $partsServer[1]) {
                    $ipServerCocok = true;
                    $jaringanCocok = $jaringan->nama_jaringan;
                    break;
                }
            }
        }

        return view('admin.pengaturan_lan.index', compact('ips', 'serverIp', 'ipServerCocok', 'jaringanCocok'));
    }

    // AJAX Toggle Switch per IP
    public function toggleStatus(Request $request, $id)
    {
        try {
            $ip = IpLokal::findOrFail($id);
            $ip->is_active = !$ip->is_active;
            $ip->save();

            return response()->json([
                'success' => true,
                'message' => 'Status jaringan berhasil diperbarui!',
                'is_active' => $ip->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status.'
            ], 500);
        }
    }

    public function create(Request $request)
    {
        $myIp = $request->ip(); 
        return view('admin.pengaturan_lan.create', compact('myIp'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jaringan' => 'required|string|max:255',
            'ip_address' => 'required|unique:ip_lokals,ip_address',
            'is_active' => 'required|boolean',
        ], [
            'ip_address.unique' => 'IP Address ini sudah terdaftar di sistem.'
        ]);

        IpLokal::create($request->all());

        return redirect()->route('admin.pengaturan-lan.index')->with('success', 'Jaringan WiFi berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $ip = IpLokal::findOrFail($id);
        return view('admin.pengaturan_lan.edit', compact('ip'));
    }

    public function update(Request $request, $id)
    {
        $ip = IpLokal::findOrFail($id);

        $request->validate([
            'nama_jaringan' => 'required|string|max:255',
            'ip_address' => 'required|unique:ip_lokals,ip_address,' . $id,
            'is_active' => 'required|boolean',
        ]);

        $ip->update($request->all());

        return redirect()->route('admin.pengaturan-lan.index')->with('success', 'Pengaturan jaringan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ip = IpLokal::findOrFail($id);
        $ip->delete();

        return redirect()->route('admin.pengaturan-lan.index')->with('success', 'Data jaringan berhasil dihapus!');
    }
}