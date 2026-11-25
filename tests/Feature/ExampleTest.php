<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_user_dashboard_renders_successfully(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Progres Bonus Tier');
    }

    public function test_user_dashboard_renders_dynamic_platform_targets_when_configured(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        \App\Models\SalaryScheme::create([
            'user_id' => $user->id,
            'hourly_rate' => 15000,
            'content_edit_rate' => 10000,
            'content_live_rate' => 5000,
            'monthly_target_hours' => 158,
            'platform_rates' => [
                'tiktok' => ['hourly_rate' => 15000, 'target_hours' => 78, 'content_edit_rate' => 10000, 'content_live_rate' => 5000],
                'shopee' => ['hourly_rate' => 20000, 'target_hours' => 80, 'content_edit_rate' => 10000, 'content_live_rate' => 5000],
            ],
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Target TikTok Live');
        $response->assertSee('Target Shopee');
        $response->assertSee('78 jam');
        $response->assertSee('80 jam');
        $response->assertSee('158 jam');
    }
}

