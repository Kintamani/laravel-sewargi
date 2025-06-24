<?php

namespace App\Interfaces;

use App\Models\User;

/**
 * Interface UserInterface
 *
 * Mendefinisikan kontrak untuk operasi data terkait pengguna.
 * Kelas yang mengimplementasikan antarmuka ini harus menyediakan
 * metode yang didefinisikan di sini.
 */
interface UserInterface
{
    public function register(array $input): User;
    public function profile(int $id): array;
}
