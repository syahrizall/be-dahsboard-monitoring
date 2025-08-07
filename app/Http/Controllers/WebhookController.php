<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginLog;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Validasi data request
        $validated = $request->validate([
            'username' => 'required|string',
            'success' => 'required|boolean',
        ]);

        // Ambil data dari RADIUS webhook (asumsi JSON)
        $data = $request->all();

        LoginLog::create([
            'username' => $data['username'] ?? 'unknown',
            'ip_address' => $request->ip(),
            'success' => $data['success'] ?? false,
            'raw_payload' => $data,
        ]);

        return response()->json([
            'status' => 'logged',
            'message' => 'Login log created successfully',
            'data' => $data,
        ], 200);
    }
}
