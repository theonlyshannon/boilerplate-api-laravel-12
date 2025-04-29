<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (!Auth::attempt($credentials)) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Email atau password salah',
                    null,
                    401
                );
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return ResponseHelper::jsonResponse(
                true,
                'Login berhasil',
                [
                    'user' => new UserResource($user),
                    'token' => $token
                ],
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Login gagal',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();

            return ResponseHelper::jsonResponse(
                true,
                'Data user berhasil diambil',
                new UserResource($user),
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Gagal mengambil data user',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return ResponseHelper::jsonResponse(
                true,
                'Logout berhasil',
                null,
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Logout gagal',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}