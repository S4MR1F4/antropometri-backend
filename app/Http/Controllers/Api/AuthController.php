<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
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

        // Auto-login: Create token immediately after registration
        $token = $user->createToken('default');
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            'model_type' => 'User',
            'model_id' => $user->id,
            'new_values' => [
                'email' => $user->email,
                'device_name' => $request->input('device_name', 'default'),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            data: [
                'user' => new UserResource($user),
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ],
            message: 'Registrasi berhasil. Selamat datang!',
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
            ActivityLog::create([
                'user_id' => $user?->id,
                'action' => 'login_failed',
                'model_type' => 'User',
                'model_id' => $user?->id,
                'new_values' => [
                    'email' => $request->email,
                    'reason' => 'invalid_credentials',
                    'device_name' => $request->input('device_name', 'default'),
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

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
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login_success',
            'model_type' => 'User',
            'model_id' => $user->id,
            'new_values' => [
                'email' => $user->email,
                'device_name' => $deviceName,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

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
        ActivityLog::log('logout', 'User', $request->user()->id, [
            'token_name' => $request->user()->currentAccessToken()?->name,
        ]);
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
            data: ['user' => new UserResource($request->user())]
        );
    }

    /**
     * Update user profile information.
     * POST /auth/update-profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($request->only('name', 'email'));

        return $this->successResponse(
            data: ['user' => new UserResource($user)],
            message: 'Profil berhasil diperbarui'
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

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse(
                message: 'Email tidak terdaftar.',
                code: 404
            );
        }

        $newPassword = Str::random(10);
        $user->update(['password' => Hash::make($newPassword)]);

        try {
            $user->notify(new \App\Notifications\PasswordResetNotification($newPassword));
        } catch (\Exception $e) {
            \Log::error('Failed to send reset email: ' . $e->getMessage());
            return $this->errorResponse(
                message: 'Gagal mengirim email reset kata sandi. Periksa konfigurasi SMTP.',
                code: 500
            );
        }

        return $this->successResponse(message: 'Kata sandi baru telah dikirim ke email Anda.');
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
