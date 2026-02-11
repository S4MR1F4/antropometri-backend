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
use Illuminate\Support\Str;

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

        try {
            $user->notify(new \App\Notifications\WelcomeNotification());
        } catch (\Exception $e) {
            // Log error but don't fail registration
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        return $this->successResponse(
            data: ['user' => new UserResource($user)],
            message: 'Registrasi berhasil. Silakan cek email Anda.',
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

    /**
     * Change user password.
     * POST /auth/change-password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse(
                message: 'Kata sandi saat ini tidak sesuai',
                code: 422
            );
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return $this->successResponse(message: 'Kata sandi berhasil diubah');
    }

    /**
     * Forgot password request.
     * POST /auth/forgot-password
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = \Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === \Password::RESET_LINK_SENT) {
            return $this->successResponse(message: 'Link reset kata sandi telah dikirim ke email Anda.');
        }

        return $this->errorResponse(
            message: 'Gagal mengirim link reset kata sandi.',
            code: 400
        );
    }

    /**
     * Reset password.
     * POST /auth/reset-password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = \Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === \Password::PASSWORD_RESET) {
            return $this->successResponse(message: 'Kata sandi berhasil diatur ulang. Silakan login kembali.');
        }

        return $this->errorResponse(
            message: 'Token tidak valid atau kadaluarsa.',
            code: 400
        );
    }
}
