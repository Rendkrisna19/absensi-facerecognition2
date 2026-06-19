<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ip_lokals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jaringan')->comment('Misal: WiFi Ruang Guru');
            $table->string('ip_address')->unique()->comment('Alamat IP Lokal/Publik');
            $table->boolean('is_active')->default(true)->comment('Status jaringan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_lokals');
    }
};