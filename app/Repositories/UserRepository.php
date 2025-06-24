<?php

namespace App\Repositories;

use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface
{
    /**
     * Mendaftarkan pengguna baru dengan mengamankan kata sandinya.
     *
     * @param array $input Array data pengguna, termasuk 'name', 'email', dan 'password'.
     * Pastikan 'password' ada di dalam array ini.
     * @return \App\Models\User Mengembalikan instance Model User yang baru dibuat.
     */
    public function register(array $input): User
    {
        if (isset($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        }
        $user = User::create($input);

        return $user;
    }

    /**
     * Mendapatkan profil pengguna.
     *
     * @return \App\Models\User
     */
    public function profile(int $id): array
    {
        $user = User::select('id', 'nik', 'email')
            ->where('id', $id)
            ->first();

        if (!$user) {
            return array();
        }

        $kependudukan = DB::connection('mysql-provider')
            ->table('s_kependudukan_anggota')
            ->select(
                "s_kependudukan_anggota.nama",
                "s_kependudukan_anggota.nomor_kk",
                "s_kependudukan_anggota.tempat_lahir",
                "s_kependudukan_anggota.tanggal_lahir",
                "s_kependudukan_anggota.nama_ayah",
                "s_kependudukan_anggota.nama_ibu"
            )
            ->where('s_kependudukan_anggota.nik', $user->nik)
            ->first();

        $data = [
            'id' => $user->id,
            'nik' => $user->nik,
            'email' => $user->email,
            'nama' => $kependudukan ? $kependudukan->nama : null,
            'tempat_lahir' => $kependudukan ? $kependudukan->tempat_lahir : null,
            'tanggal_lahir' => $kependudukan ? $kependudukan->tanggal_lahir : null,
            'nama_ayah' => $kependudukan ? $kependudukan->nama_ayah : null,
            'nama_ibu' => $kependudukan ? $kependudukan->nama_ibu : null,
        ];

        return $data;
    }
}
