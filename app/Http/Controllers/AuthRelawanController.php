<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;

class AuthRelawanController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nama_lengkap_pengguna'     => 'required|string|max:255',
            'username_akun_pengguna'    => 'required|string|unique:penggunas',
            'password_akun_pengguna'    => 'required|string|min:6',
        ]);

        // Map web app field names to API format
        $userData = [
            'name' => $request->nama_lengkap_pengguna,
            'email' => $request->username_akun_pengguna, // Use username as email
            'password' => $request->password_akun_pengguna,
            'password_confirmation' => $request->password_akun_pengguna,
            'role' => 'volunteer' // Default role for web registrations
        ];

        $result = $this->authService->register($userData);

        if ($result['success']) {
            return response()->json(['message' => 'User registered successfully!']);
        } else {
            return response()->json(['message' => $result['message']], 422);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username_akun_pengguna'    => 'required',
            'password_akun_pengguna'    => 'required',
        ]);

        // Map web app field names to API format
        $credentials = [
            'email' => $request->username_akun_pengguna,
            'password' => $request->password_akun_pengguna,
        ];

        $result = $this->authService->login($credentials);

        if ($result['success']) {
            return response()->json([
                'message' => 'Login successful',
                'token'   => $result['data']['token'],
                'user'    => $result['data']['user']
            ]);
        } else {
            return response()->json(['message' => $result['message']], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logout();

        if ($result['success']) {
            return response()->json(['message' => 'Logged out']);
        } else {
            return response()->json(['message' => $result['message']], 500);
        }
    }
}
