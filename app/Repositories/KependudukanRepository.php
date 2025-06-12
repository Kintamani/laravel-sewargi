<?php

namespace App\Repositories;

use App\Interfaces\KependudukanInterface;
use Illuminate\Support\Facades\DB;

class KependudukanRepository implements KependudukanInterface
{
    /**
     * Mengambil data kependudukan berdasarkan Nomor Induk Kependudukan (NIK).
     *
     * @param string $nik Nomor Induk Kependudukan (NIK) yang akan dicari.
     * @return array|null Mengembalikan object data kependudukan jika ditemukan,
     * atau null jika NIK tidak ditemukan.
     */
    public function getNik($nik): ?object
    {
        $dbConnection = DB::connection('mysql-provider');
        $data = $dbConnection
            ->table('s_kependudukan_anggota')
            ->select(
                "s_kependudukan_anggota.nik",
                "s_kependudukan_anggota.nama",
                "s_kependudukan_anggota.nomor_kk",
                "s_kependudukan.alamat",
                "s_kependudukan_anggota.nama",
                "s_kependudukan_anggota.tempat_lahir",
                "s_kependudukan_anggota.tanggal_lahir",
                "s_kependudukan_anggota.nama_ayah",
                "s_kependudukan_anggota.nama_ibu",
                "s_kependudukan_anggota.memiliki_usaha",
                "m_yatim_piatu.nama AS yatim_piatu",
                "m_jenis_kelamin.nama AS jenis_kelamin",
                "m_jenis_kontrasepsi.nama AS jenis_kontrasepsi",
                "m_agama.nama AS agama",
                "m_pendidikan.nama AS pendidikan",
                "m_pekerjaan.nama AS pekerjaan",
                "m_status_pernikahan.nama AS status_pernikahan",
                "m_hubungan.hubungan AS status_hubungan",
                "m_kewarganegaraan.nama AS kewarganegaraan",
                "m_golongan_darah.nama AS golongan_darah",
                "s_kependudukan_anggota.periode_awal",
                "s_kependudukan_anggota.periode_akhir",
                "s_kependudukan.kd_provinsi",
                "mapwil.propinsi",
                "s_kependudukan.kd_kota AS kode_kota",
                "mapwil.kabupaten AS kota",
                "s_kependudukan.kd_kecamatan AS kode_kecamatan",
                "mapwil.kecamatan",
                "s_kependudukan.kd_kelurahan AS kode_kelurahan",
                "mapwil.kelurahan",
                "s_kependudukan.rw",
                "s_kependudukan.rt",
            )
            ->leftJoin('s_kependudukan', 's_kependudukan_anggota.s_kependudukan_id', '=', 's_kependudukan.id')
            ->leftJoin('m_yatim_piatu', 's_kependudukan_anggota.yatim_piatu_id', '=', 'm_yatim_piatu.id')
            ->leftJoin('m_jenis_kelamin', 's_kependudukan_anggota.jenis_kelamin_id', '=', 'm_jenis_kelamin.id')
            ->leftJoin('m_jenis_kontrasepsi', 's_kependudukan_anggota.jenis_kontrasepsi_id', '=', 'm_jenis_kontrasepsi.id')
            ->leftJoin('m_agama', 's_kependudukan_anggota.agama_id', '=', 'm_agama.id')
            ->leftJoin('m_pendidikan', 's_kependudukan_anggota.pendidikan_id', '=', 'm_pendidikan.id')
            ->leftJoin('m_pekerjaan', 's_kependudukan_anggota.pekerjaan_id', '=', 'm_pekerjaan.id')
            ->leftJoin('m_status_pernikahan', 's_kependudukan_anggota.status_pernikahan_id', '=', 'm_status_pernikahan.id')
            ->leftJoin('m_hubungan', 's_kependudukan_anggota.status_hubungan_id', '=', 'm_hubungan.id')
            ->leftJoin('m_kewarganegaraan', 's_kependudukan_anggota.kewarganegaraan_id', '=', 'm_kewarganegaraan.id')
            ->leftJoin('m_golongan_darah', 's_kependudukan_anggota.golongan_darah_id', '=', 'm_golongan_darah.id')
            ->leftJoin('mapwil', function ($join) {
                $join->on('s_kependudukan.kd_provinsi', '=', 'mapwil.kd_propinsi')
                    ->on('s_kependudukan.kd_kota', '=', 'mapwil.kd_kabupaten')
                    ->on('s_kependudukan.kd_kecamatan', '=', 'mapwil.kd_kecamatan')
                    ->on('s_kependudukan.kd_kelurahan', '=', 'mapwil.kd_kelurahan');
            })
            ->where('s_kependudukan_anggota.nik', $nik)
            ->first();

        return $data ? $data : null;
    }

    /**
     * Mengambil data anggota keluarga berdasarkan Kartu Keluarga (KK).
     *
     * @param string $kk Kartu Keluarga (KK) yang akan dicari.
     * @return array|null Mengembalikan array data Keluarga jika ditemukan,
     * atau null jika KK tidak ditemukan.
     */
    public function getKk($kk): ?array
    {
        $dbConnection = DB::connection('mysql-provider');
        $data = $dbConnection
            ->table('s_kependudukan_anggota')
            ->select("nama", "nik", "id", "created_at", "updated_at")
            ->where('s_kependudukan_anggota.nomor_kk', $kk)
            ->get();

        return $data ? $data->toArray() : null;
    }
}
