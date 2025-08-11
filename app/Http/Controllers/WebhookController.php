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
        Log::info('Webhook received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
            'source' => 'privacyidea'
        ]);
        try {
            // Log raw request untuk debugging
            Log::info('Webhook received', [
                'headers' => $request->headers->all(),
                'payload' => $request->all(),
                'source' => 'privacyidea'
            ]);

            // Validasi data request untuk PrivacyIDEA
            $validated = $request->validate([
                'username' => 'required|string|max:255',
                'success' => 'required|boolean',
                'client_ip' => 'nullable|ip',
                'realm' => 'nullable|string|max:255',
                'resolver' => 'nullable|string|max:255',
                'token_type' => 'nullable|string|max:100',
                'serial' => 'nullable|string|max:100',
                'action' => 'nullable|string|max:100',
                'raw_payload' => 'nullable|array',
            ]);

            // Ambil data dari PrivacyIDEA webhook
            $data = $request->all();

            // Mapping field PrivacyIDEA ke field internal
            $loginLogData = [
                'username' => $data['username'] ?? 'unknown',
                'ip_address' => $data['client_ip'] ?? $request->ip(), // PrivacyIDEA menggunakan client_ip
                'success' => $data['success'] ?? false,
                'realm' => $data['realm'] ?? null,
                'resolver' => $data['resolver'] ?? null,
                'token_type' => $data['token_type'] ?? null,
                'serial' => $data['serial'] ?? null,
                'action' => $data['action'] ?? null,
                'raw_payload' => $data,
            ];

            $loginLog = $this->loginLogService->createLoginLog($loginLogData);

            Log::info('PrivacyIDEA login log created', [
                'username' => $loginLog->username,
                'success' => $loginLog->success,
                'ip_address' => $loginLog->ip_address,
                'realm' => $loginLog->realm,
                'token_type' => $loginLog->token_type,
                'action' => $loginLog->action,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'PrivacyIDEA login log created successfully',
                'data' => $loginLog,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('PrivacyIDEA webhook validation failed', [
                'errors' => $e->errors(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('PrivacyIDEA webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Endpoint khusus untuk PrivacyIDEA webhook
     */
    public function privacyidea(Request $request): JsonResponse
    {
        return $this->receive($request);
    }
}
