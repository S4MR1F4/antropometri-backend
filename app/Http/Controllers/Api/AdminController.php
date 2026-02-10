<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\StatisticsService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * AdminController per 07_api_specification.md §6
 * Extended with full user CRUD for Phase 4
 */
class AdminController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected StatisticsService $statisticsService
    ) {
    }

    /**
     * Get dashboard statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->statisticsService->getDashboardStats($request->only(['from_date', 'to_date', 'category']));

        return $this->successResponse(
            data: $stats,
            message: 'Statistik dashboard berhasil dimuat'
        );
    }

    /**
     * List all users.
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->integer('per_page', 15));

        return $this->successResponse(
            data: [
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
            ]
        );
    }

    /**
     * Create a new user (petugas).
     */
    public function storeUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|in:petugas,admin',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                message: 'Data tidak valid',
                errors: $validator->errors()->toArray(),
                code: 422
            );
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role', 'petugas'),
        ]);

        ActivityLog::log('admin_create_user', 'User', $user->id, $user->toArray());

        return $this->successResponse(
            data: ['user' => $user],
            message: 'Petugas berhasil ditambahkan',
            code: 201
        );
    }

    /**
     * Update an existing user.
     */
    public function updateUser(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|min:3|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|in:petugas,admin',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                message: 'Data tidak valid',
                errors: $validator->errors()->toArray(),
                code: 422
            );
        }

        $oldValues = $user->toArray();

        $updateData = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->input('password'));
        }

        $user->update($updateData);

        ActivityLog::log(
            'admin_update_user',
            'User',
            $user->id,
            $user->fresh()->toArray(),
            $oldValues
        );

        return $this->successResponse(
            data: ['user' => $user->fresh()],
            message: 'User berhasil diperbarui'
        );
    }

    /**
     * Delete a user (soft delete).
     */
    public function destroyUser(User $user): JsonResponse
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return $this->errorResponse(
                message: 'Tidak dapat menghapus akun sendiri',
                code: 403
            );
        }

        $oldValues = $user->toArray();
        $user->delete();

        ActivityLog::log('admin_delete_user', 'User', $user->id, null, $oldValues);

        return $this->successResponse(
            message: 'User berhasil dihapus'
        );
    }
}
