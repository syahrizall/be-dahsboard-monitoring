<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\LoginLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebhookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_receives_valid_data()
    {
        $data = [
            'username' => 'testuser',
            'success' => true,
            'ip_address' => '192.168.1.1',
        ];

        $response = $this->postJson('/api/webhook', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'username',
                    'ip_address',
                    'success',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('login_logs', [
            'username' => 'testuser',
            'success' => true,
            'ip_address' => '192.168.1.1',
        ]);
    }

    public function test_webhook_validates_required_fields()
    {
        $response = $this->postJson('/api/webhook', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors'
            ]);
    }

    public function test_webhook_validates_username_type()
    {
        $data = [
            'username' => 123, // Should be string
            'success' => true,
        ];

        $response = $this->postJson('/api/webhook', $data);

        $response->assertStatus(422);
    }

    public function test_webhook_validates_success_type()
    {
        $data = [
            'username' => 'testuser',
            'success' => 'not_boolean', // Should be boolean
        ];

        $response = $this->postJson('/api/webhook', $data);

        $response->assertStatus(422);
    }

    public function test_webhook_handles_missing_optional_fields()
    {
        $data = [
            'username' => 'testuser',
            'success' => false,
        ];

        $response = $this->postJson('/api/webhook', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('login_logs', [
            'username' => 'testuser',
            'success' => false,
        ]);
    }

    public function test_webhook_stores_raw_payload()
    {
        $data = [
            'username' => 'testuser',
            'success' => true,
            'custom_field' => 'custom_value',
        ];

        $response = $this->postJson('/api/webhook', $data);

        $response->assertStatus(201);

        $loginLog = LoginLog::first();
        $this->assertEquals($data, $loginLog->raw_payload);
    }
}
