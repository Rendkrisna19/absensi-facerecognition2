<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    // Hanya kolom 'id' yang dijaga, sisanya boleh diisi massal (mass assignment)
    protected $guarded = ['id'];

    // Relasi balik (Belongs To) ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}