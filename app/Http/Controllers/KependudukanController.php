<?php

namespace App\Http\Controllers;

use App\Repositories\KependudukanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class KependudukanController extends Controller 
{
    /**
     * @var KependudukanRepository
     */
    protected $kependudukanRepository;

    /**
     * Constructor untuk Dependency Injection.
     * Secara otomatis Laravel akan menginject instance dari KependudukanRepository.
     *
     * @param KependudukanRepository $kependudukanRepository
     */
    public function __construct(KependudukanRepository $kependudukanRepository)
    {
        $this->kependudukanRepository = $kependudukanRepository;
    }

    /**
     * Memvalidasi NIK dan mengambil data kependudukan.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function nik(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nik' => "required|numeric|digits:16",
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors()->first('nik')
            ], 400);
        }

        try {
            $nik = $request->input('nik');
            $dataKependudukan = $this->kependudukanRepository->getNik($nik);

            if (empty($dataKependudukan)) {
                return response()->json([
                    'message' => 'Data not found.',
                ], 404); 
            }

            return response()->json([
                'data' => $dataKependudukan,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error.',
            ], 500);
        }
    }

    /**
     * mendapatkan anggota keluragra berdasarkan Nomor KK.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function kk(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'kk' => 'required|numeric|digits:16',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors()->first('kk')
            ], 400);
        }

        try {
            $kk = $request->query('kk');
            $dataKependudukan = $this->kependudukanRepository->getKK($kk);

            if (empty($dataKependudukan)) {
                return response()->json([
                    'message' => 'Data not found.',
                ], 404); 
            }

            return response()->json([
                'data' => $dataKependudukan,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error.',
            ], 500);
        }
    }
}
