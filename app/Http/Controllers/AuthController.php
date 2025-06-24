<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * Class AuthController
 *
 * Mengelola otentikasi pengguna, termasuk registrasi, login, logout,
 * dan pengambilan profil pengguna.
 */
class AuthController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Constructor untuk Dependency Injection.
     * Secara otomatis Laravel akan menginject instance dari UserRepository.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Mendaftarkan pengguna baru ke sistem.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nik' => 'required|numeric|digits:16|unique:users,nik',
            'password' => 'required|string|min:6',
            'email' => 'required|string|email|unique:users,email',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors(),
            ], 400);
        }

        try {
            $input = [
                'nik' => $request->nik,
                'password' => $request->password,
                'email' => $request->email,
            ];

            $user = $this->userRepository->register($input);
            $token = JWTAuth::fromUser($user);
            return response()->json([
                'data' => [
                    'token' => $token,
                    'user' => $user->only(['id', 'nik', 'email', 'name']),
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ],
            ], 201);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to generate token.',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to register user.',
            ], 500);
        }
    }

    /**
     * Melakukan login pengguna dan mengembalikan token JWT.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nik' => 'required|numeric|digits:16',
            'password' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors(),
            ], 400);
        }

        $credentials = $request->only('nik', 'password');
        $token = null;

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'NIK or Password is invalid.',
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to generate token.',
            ], 500);
        }

        return response()->json([
            'data' => [
                'token' => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ], 200);
    }

    /**
     * Melakukan logout pengguna dengan menginvalidasi token JWT.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'message' => 'Successfully logged out.',
            ], 200);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Token Expired, but successfully logged out.',
            ], 200);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'Token not valid, but successfully logged out.',
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Filed to logout, please try again.',
            ], 500);
        }
    }

    /**
     * Mendapatkan profil pengguna.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $user = auth('api')->user();
        try {
            $data = $this->userRepository->profile($user->id);
            return response()->json([
                'data' => $data,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to get profile.',
            ], 500);
        }
    }
}
