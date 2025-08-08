<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatsApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsSanctumUser(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_active_users_endpoint()
    {
        $this->actingAsSanctumUser();
        // Create test data
        LoginLog::create([
            'username' => 'testuser1',
            'ip_address' => '192.168.1.1',
            'success' => true,
        ]);

        $response = $this->get('/api/stats/active-users');

        $response->assertStatus(200)
            ->assertJsonStructure(['active_users']);
    }

    public function test_unique_users_endpoint()
    {
        $this->actingAsSanctumUser();
        // Create test data
        LoginLog::create([
            'username' => 'testuser1',
            'ip_address' => '192.168.1.1',
            'success' => true,
        ]);

        LoginLog::create([
            'username' => 'testuser2',
            'ip_address' => '192.168.1.2',
            'success' => true,
        ]);

        $response = $this->get('/api/stats/unique-users');

        $response->assertStatus(200)
            ->assertJson(['unique_users' => 2]);
    }

    public function test_success_logins_endpoint()
    {
        $this->actingAsSanctumUser();
        // Create test data
        LoginLog::create([
            'username' => 'testuser1',
            'ip_address' => '192.168.1.1',
            'success' => true,
        ]);

        LoginLog::create([
            'username' => 'testuser2',
            'ip_address' => '192.168.1.2',
            'success' => false,
        ]);

        $response = $this->get('/api/stats/success-logins');

        $response->assertStatus(200)
            ->assertJson(['success_logins' => 1]);
    }

    public function test_failed_logins_endpoint()
    {
        $this->actingAsSanctumUser();
        // Create test data
        LoginLog::create([
            'username' => 'testuser1',
            'ip_address' => '192.168.1.1',
            'success' => true,
        ]);

        LoginLog::create([
            'username' => 'testuser2',
            'ip_address' => '192.168.1.2',
            'success' => false,
        ]);

        $response = $this->get('/api/stats/failed-logins');

        $response->assertStatus(200)
            ->assertJson(['failed_logins' => 1]);
    }

    public function test_logins_by_date_endpoint()
    {
        $this->actingAsSanctumUser();
        $response = $this->get('/api/stats/logins-by-date?from=2024-01-01&to=2024-01-31');

        $response->assertStatus(200)
            ->assertJsonStructure(['*' => ['date', 'total']]);
    }

    public function test_logins_by_date_validation()
    {
        $this->actingAsSanctumUser();
        $response = $this->get('/api/stats/logins-by-date');

        $response->assertStatus(422)
            ->assertJsonStructure(['error', 'errors']);
    }
}
