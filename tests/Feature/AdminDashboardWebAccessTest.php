<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardWebAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_open_the_browser_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Admin dashboard');
    }

    public function test_non_admin_user_is_blocked_from_the_browser_dashboard(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }
}
