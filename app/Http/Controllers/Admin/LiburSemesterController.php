<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LiburSemester;
use Carbon\Carbon;

class LiburSemesterController extends Controller
{
    public function index()
    {
        $liburs = LiburSemester::orderBy('tanggal_mulai', 'desc')->get();
        return view('admin.libur.index', compact('liburs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_semester' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        LiburSemester::create($request->all());
        return back()->with('success', 'Jadwal libur semester berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $libur = LiburSemester::findOrFail($id);
        $request->validate([
            'nama_semester' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $libur->update($request->all());
        return back()->with('success', 'Jadwal libur berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $libur = LiburSemester::findOrFail($id);
        $libur->delete();
        return back()->with('success', 'Jadwal libur berhasil dihapus!');
    }
}