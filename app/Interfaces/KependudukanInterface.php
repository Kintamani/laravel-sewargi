<?php

namespace App\Interfaces;

/**
 * Interface KependudukanInterface
 *
 * Mendefinisikan kontrak untuk layanan atau repository kependudukan.
 * Kelas yang mengimplementasikan antarmuka ini harus menyediakan
 * metode yang didefinisikan di sini.
 */
interface KependudukanInterface {
    public function getNik($nik): ?object;
    public function getKk($kk): ?array;
}