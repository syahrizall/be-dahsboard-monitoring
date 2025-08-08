<?php

namespace App\Services;

use App\Models\LoginLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LoginLogService
{
    /**
     * Get active users (logged in within last 15 minutes)
     */
    public function getActiveUsers(): Collection
    {
        $cutoff = now()->subMinutes(15);
        return LoginLog::where('success', true)
            ->where('created_at', '>=', $cutoff)
            ->distinct('username')
            ->pluck('username');
    }

    /**
     * Get count of unique users
     */
    public function getUniqueUsersCount(): int
    {
        return LoginLog::distinct('username')->count('username');
    }

    /**
     * Get list of unique users
     */
    public function getUniqueUsersList(): Collection
    {
        return LoginLog::select('username')->distinct()->get();
    }

    /**
     * Get last login data for each user
     */
    public function getLastLoginData(): Collection
    {
        return LoginLog::where('success', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('username')
            ->values();
    }

    /**
     * Get count of successful logins
     */
    public function getSuccessCount(): int
    {
        return LoginLog::where('success', true)->count();
    }

    /**
     * Get count of failed logins
     */
    public function getFailedCount(): int
    {
        return LoginLog::where('success', false)->count();
    }

    /**
     * Get login statistics by date range
     */
    public function getLoginsByDate(string $from, string $to): Collection
    {
        return LoginLog::whereBetween('created_at', [$from, $to])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Create a new login log entry
     */
    public function createLoginLog(array $data): LoginLog
    {
        return LoginLog::create($data);
    }
}
