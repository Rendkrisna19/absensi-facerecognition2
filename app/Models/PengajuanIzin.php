<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanIzin extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit (opsional tapi disarankan)
    protected $table = 'pengajuan_izins';

    // Mass assignment protection
    protected $fillable = [
        'user_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'file_bukti',
        'status',
        'catatan_penolakan',
        'disetujui_oleh',
        'direspon_pada',
    ];

    // Casting tipe data agar lebih mudah diolah (contoh: jadi Carbon instance)
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'direspon_pada' => 'datetime',
    ];

    /**
     * Relasi ke User yang mengajukan cuti/izin.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User (Admin/Kepala) yang merespon pengajuan.
     */
    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}