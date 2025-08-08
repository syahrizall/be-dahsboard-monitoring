<?php

namespace App\Http\Controllers;

use App\Services\LoginLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class WebhookController extends Controller
{
    protected LoginLogService $loginLogService;

    public function __construct(LoginLogService $loginLogService)
    {
        $this->loginLogService = $loginLogService;
    }

    public function receive(Request $request): JsonResponse
    {
        try {
            // Validasi data request
            $validated = $request->validate([
                'username' => 'required|string|max:255',
                'success' => 'required|boolean',
                'ip_address' => 'nullable|ip',
                'raw_payload' => 'nullable|array',
            ]);

            // Ambil data dari RADIUS webhook
            $data = $request->all();

            $loginLog = $this->loginLogService->createLoginLog([
                'username' => $data['username'] ?? 'unknown',
                'ip_address' => $data['ip_address'] ?? $request->ip(),
                'success' => $data['success'] ?? false,
                'raw_payload' => $data,
            ]);

            Log::info('Login log created', [
                'username' => $loginLog->username,
                'success' => $loginLog->success,
                'ip_address' => $loginLog->ip_address,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Login log created successfully',
                'data' => $loginLog,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Webhook validation failed', [
                'errors' => $e->errors(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
            ], 500);
        }
    }
}
