<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiburSemester extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'libur_semesters';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'nama_semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'is_active'
    ];

    // Mengubah string tanggal otomatis menjadi objek Carbon
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk mengecek apakah saat ini sedang dalam masa libur
     * Bisa dipanggil di Controller: LiburSemester::isLiburSekarang()
     */
    public static function isLiburSekarang()
    {
        return self::where('is_active', true)
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->exists();
    }
}