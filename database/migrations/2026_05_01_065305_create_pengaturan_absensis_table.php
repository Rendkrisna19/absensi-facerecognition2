<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_absensis', function (Blueprint $table) {
            $table->id();
            $table->time('jam_buka_absen')->default('06:00:00')->comment('Jam scanner absen pagi mulai dibuka');
            $table->time('batas_jam_masuk')->default('07:15:00')->comment('Lewat dari jam ini dianggap Terlambat & Kena Denda');
            $table->time('jam_pulang')->default('14:00:00')->comment('Jam scanner absen pulang dibuka');
            $table->integer('denda_terlambat')->default(10000)->comment('Nominal denda FLAT per keterlambatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_absensis');
    }
};