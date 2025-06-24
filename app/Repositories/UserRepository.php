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
    public function profile(int $id): User
    {
        $user = User::select('id', 'nik', 'email', 'name')
            ->where('id', $id)
            ->first();
        return $user;
    }
}
