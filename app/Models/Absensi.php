<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
    'user_id',
    'tanggal',
    'jam_masuk',
    'jam_pulang', // <-- Tambahkan ini
    'status',
    'menit_terlambat'
];
    
}