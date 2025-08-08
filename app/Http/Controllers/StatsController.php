<?php

namespace App\Http\Controllers;

use App\Services\LoginLogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
{
    protected LoginLogService $loginLogService;

    public function __construct(LoginLogService $loginLogService)
    {
        $this->loginLogService = $loginLogService;
    }

    public function activeUsers(): JsonResponse
    {
        try {
            $users = $this->loginLogService->getActiveUsers();
            return response()->json(['active_users' => $users]);
        } catch (\Exception $e) {
            Log::error('Error fetching active users', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch active users'], 500);
        }
    }

    public function uniqueUsers(): JsonResponse
    {
        try {
            $count = $this->loginLogService->getUniqueUsersCount();
            return response()->json(['unique_users' => $count]);
        } catch (\Exception $e) {
            Log::error('Error fetching unique users', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch unique users'], 500);
        }
    }

    public function listUniqueUsers(): JsonResponse
    {
        try {
            $users = $this->loginLogService->getUniqueUsersList();
            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Error fetching unique users list', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch unique users list'], 500);
        }
    }

    public function lastLogin(): JsonResponse
    {
        try {
            $data = $this->loginLogService->getLastLoginData();
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error fetching last login data', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch last login data'], 500);
        }
    }

    public function successCount(): JsonResponse
    {
        try {
            $count = $this->loginLogService->getSuccessCount();
            return response()->json(['success_logins' => $count]);
        } catch (\Exception $e) {
            Log::error('Error fetching success count', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch success count'], 500);
        }
    }

    public function failedCount(): JsonResponse
    {
        try {
            $count = $this->loginLogService->getFailedCount();
            return response()->json(['failed_logins' => $count]);
        } catch (\Exception $e) {
            Log::error('Error fetching failed count', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch failed count'], 500);
        }
    }

    public function loginsByDate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from',
            ]);

            $from = $request->input('from');
            $to = $request->input('to');

            $data = $this->loginLogService->getLoginsByDate($from, $to);
            return response()->json($data);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Invalid date parameters', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error fetching logins by date', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch logins by date'], 500);
        }
    }
}
