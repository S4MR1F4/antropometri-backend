<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::query()->with('user')->latest();

        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('level')) {
            $query->where('new_values->level', $request->input('level'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('model_type', 'like', "%{$search}%")
                    ->orWhere('new_values', 'like', "%{$search}%")
                    ->orWhere('old_values', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate($request->integer('per_page', 30));

        return $this->successResponse(data: [
            'logs' => collect($logs->items())->map(fn(ActivityLog $log) => [
                'id' => $log->id,
                'user' => $log->user ? (new UserResource($log->user))->resolve() : null,
                'action' => $log->action,
                'model_type' => $log->model_type,
                'model_id' => $log->model_id,
                'old_values' => $log->old_values,
                'new_values' => $log->new_values,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'created_at' => $log->created_at?->toIso8601String(),
            ])->values(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ]);
    }

    public function storeClientLog(Request $request): JsonResponse
    {
        $data = $request->validate([
            'level' => ['required', 'string', 'in:info,warning,error'],
            'action' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
            'context' => ['nullable', 'array'],
            'occurred_at' => ['nullable', 'date'],
        ]);

        $log = ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'client_' . $data['action'],
            'model_type' => 'ClientLog',
            'model_id' => null,
            'new_values' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            data: ['log_id' => $log->id],
            message: 'Log client tersimpan',
            code: 201
        );
    }
}
