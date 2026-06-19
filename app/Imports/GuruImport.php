<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Guru;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Pastikan baris memiliki data minimal nama dan NIK
                if (empty($row['nama']) || empty($row['nik'])) {
                    continue;
                }

                $user = User::where('nik', $row['nik'])->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $row['nama'],
                        'username' => $row['nik'],
                        'nik' => $row['nik'],
                        'password' => Hash::make('password123'),
                        'role' => 'guru',
                        'jabatan' => $row['jabatan'] ?? 'guru',
                        'unit_sekolah' => $row['unit_sekolah'] ?? 'Umum',
                    ]);
                } else {
                    $user->update([
                        'name' => $row['nama'],
                        'jabatan' => $row['jabatan'] ?? $user->jabatan,
                        'unit_sekolah' => $row['unit_sekolah'] ?? $user->unit_sekolah,
                    ]);
                }

                Guru::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'jenis_kelamin' => $row['jenis_kelamin'] ?? $user->guru?->jenis_kelamin,
                        'tempat_lahir' => $row['tempat_lahir'] ?? $user->guru?->tempat_lahir,
                        'tanggal_lahir' => $row['tanggal_lahir'] ?? $user->guru?->tanggal_lahir,
                        'agama' => $row['agama'] ?? $user->guru?->agama,
                        'tanggal_bergabung' => $row['tanggal_bergabung'] ?? $user->guru?->tanggal_bergabung,
                        'no_hp' => $row['no_hp'] ?? $user->guru?->no_hp,
                        'pendidikan_terakhir' => $row['pendidikan_terakhir'] ?? $user->guru?->pendidikan_terakhir,
                        'alamat' => $row['alamat'] ?? $user->guru?->alamat,
                    ]
                );
            }
        });
    }
}
