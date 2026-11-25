<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('user.performance.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_top_performance_page(): void
    {
        $user1 = User::factory()->create([
            'name' => 'Host Pertama',
            'role' => 'user',
            'is_active' => true,
            'task' => 'Host Live',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Host Kedua',
            'role' => 'user',
            'is_active' => true,
            'task' => 'Host Live',
        ]);

        // Create some attendances
        Attendance::create([
            'user_id' => $user1->id,
            'attendance_date' => Carbon::now()->toDateString(),
            'live_duration_minutes' => 180,
            'sales_count' => 50,
            'content_edit_count' => 2,
            'content_live_count' => 1,
            'status' => 'validated',
            'platform' => 'tiktok',
        ]);

        Attendance::create([
            'user_id' => $user2->id,
            'attendance_date' => Carbon::now()->toDateString(),
            'live_duration_minutes' => 120,
            'sales_count' => 30,
            'content_edit_count' => 1,
            'content_live_count' => 0,
            'status' => 'validated',
            'platform' => 'tiktok',
        ]);

        $response = $this->actingAs($user1)->get(route('user.performance.index'));

        $response->assertStatus(200);
        $response->assertSee('Top Performa Host');
        $response->assertSee('Performa Saya', false);
        $response->assertSee('Host Pertama');
        $response->assertSee('Host Kedua');
        $response->assertSee('Peringkat Host');
        $response->assertSee('Anda');
    }

    public function test_user_can_filter_performance_by_category(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('user.performance.index', ['category' => 'sales']));

        $response->assertStatus(200);
        $response->assertSee('Penjualan');
    }

    public function test_user_can_get_user_detail_without_sensitive_info(): void
    {
        $user1 = User::factory()->create([
            'name' => 'Dina Istifada',
            'email' => 'dina@example.com',
            'phone' => '081234567890',
            'role' => 'user',
            'is_active' => true,
        ]);

        $user2 = User::factory()->create([
            'name' => 'Host Lain',
            'email' => 'hostlain@example.com',
            'phone' => '089876543210',
            'role' => 'user',
            'is_active' => true,
        ]);

        Attendance::create([
            'user_id' => $user2->id,
            'attendance_date' => Carbon::now()->toDateString(),
            'live_duration_minutes' => 150,
            'sales_count' => 25,
            'content_edit_count' => 3,
            'content_live_count' => 1,
            'status' => 'validated',
            'platform' => 'tiktok',
        ]);

        $response = $this->actingAs($user1)->get(route('user.performance.user-detail', $user2));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'user' => [
                'id' => $user2->id,
                'name' => 'Host Lain',
            ],
        ]);

        // Email and phone should be hidden from host view for privacy
        $json = $response->json();
        $this->assertArrayNotHasKey('email', $json['user']);
        $this->assertArrayNotHasKey('phone', $json['user']);
    }

    public function test_admin_performance_still_functions(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.performance.index'));

        $response->assertStatus(200);
        $response->assertSee('Top Performa Host');
    }
}
