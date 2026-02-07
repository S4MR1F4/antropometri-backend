<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Auth controller for authentication endpoints.
 * Per 07_api_specification.md §2
 */
class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user.
     * POST /auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'petugas',
        ]);

        return $this->successResponse(
            data: ['user' => new UserResource($user)],
            message: 'Registrasi berhasil. Silakan verifikasi email Anda.',
            code: 201
        );
    }

    /**
     * Login user and create token.
     * POST /auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse(
                message: 'Email atau password salah',
                code: 401
            );
        }

        // Delete old tokens for this device
        $deviceName = $request->device_name ?? 'default';
        $user->tokens()->where('name', $deviceName)->delete();

        // Create new token
        $token = $user->createToken($deviceName);

        return $this->successResponse(
            data: [
                'user' => new UserResource($user),
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => now()->addDays(7)->toIso8601String(),
            ],
            message: 'Login berhasil'
        );
    }

    /**
     * Logout user (revoke current token).
     * POST /auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            message: 'Logout berhasil'
        );
    }

    /**
     * Get current authenticated user.
     * GET /auth/me
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            data: new UserResource($request->user())
        );
    }
}
