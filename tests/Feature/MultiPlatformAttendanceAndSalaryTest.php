<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\SalaryScheme;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiPlatformAttendanceAndSalaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_marketplaces_configuration(): void
    {
        $all = Setting::getMarketplaces();
        $this->assertArrayHasKey('tiktok', $all);
        $this->assertArrayHasKey('shopee', $all);
        $this->assertArrayHasKey('tokopedia', $all);
        $this->assertArrayHasKey('lazada', $all);

        $active = Setting::getActiveMarketplaces();
        $this->assertArrayHasKey('tiktok', $active);
        $this->assertArrayHasKey('shopee', $active);
    }

    public function test_salary_scheme_platform_rates_and_breakdown(): void
    {
        $user = User::factory()->create();
        $scheme = SalaryScheme::create([
            'user_id' => $user->id,
            'hourly_rate' => 20000,
            'platform_rates' => [
                'tiktok' => [
                    'hourly_rate' => 15000,
                    'content_edit_rate' => 10000,
                    'content_live_rate' => 5000,
                    'target_hours' => 50,
                ],
                'shopee' => [
                    'hourly_rate' => 25000,
                    'content_edit_rate' => 12000,
                    'content_live_rate' => 6000,
                    'target_hours' => 30,
                ],
            ],
            'content_edit_rate' => 10000,
            'content_live_rate' => 5000,
            'monthly_target_hours' => 80,
            'bonus_pcs_threshold' => 20,
            'bonus_amount' => 10000,
        ]);

        $this->assertEquals(15000, $scheme->getHourlyRateForPlatform('tiktok'));
        $this->assertEquals(10000, $scheme->getContentEditRateForPlatform('tiktok'));
        $this->assertEquals(5000, $scheme->getContentLiveRateForPlatform('tiktok'));
        $this->assertEquals(50, $scheme->getTargetHoursForPlatform('tiktok'));

        $this->assertEquals(25000, $scheme->getHourlyRateForPlatform('shopee'));
        $this->assertEquals(12000, $scheme->getContentEditRateForPlatform('shopee'));
        $this->assertEquals(6000, $scheme->getContentLiveRateForPlatform('shopee'));
        $this->assertEquals(30, $scheme->getTargetHoursForPlatform('shopee'));

        // Fallback for platform not in platform_rates is base rates
        $this->assertEquals(20000, $scheme->getHourlyRateForPlatform('lazada'));
        $this->assertEquals(10000, $scheme->getContentEditRateForPlatform('lazada'));
        $this->assertEquals(5000, $scheme->getContentLiveRateForPlatform('lazada'));
        $this->assertEquals(80, $scheme->getTargetHoursForPlatform('lazada'));

        // Create 2 attendances on same date: TikTok (3 hrs, 2 edit, 1 live) and Shopee (2 hrs, 1 edit, 2 live)
        $attTikTok = Attendance::create([
            'user_id' => $user->id,
            'attendance_date' => now()->toDateString(),
            'session_name' => 'Sesi 1',
            'platform' => 'tiktok',
            'live_duration_minutes' => 180, // 3 hours * 15.000 = 45.000
            'content_edit_count' => 2,      // 2 * 10.000 = 20.000
            'content_live_count' => 1,      // 1 * 5.000 = 5.000
            'status' => 'validated',
        ]);

        $attShopee = Attendance::create([
            'user_id' => $user->id,
            'attendance_date' => now()->toDateString(),
            'session_name' => 'Sesi 1',
            'platform' => 'shopee',
            'live_duration_minutes' => 120, // 2 hours * 25.000 = 50.000
            'content_edit_count' => 1,      // 1 * 12.000 = 12.000
            'content_live_count' => 2,      // 2 * 6.000 = 12.000
            'status' => 'validated',
        ]);

        $attendances = collect([$attTikTok, $attShopee]);
        $breakdown = $scheme->getPlatformBreakdown($attendances);

        $this->assertArrayHasKey('tiktok', $breakdown);
        $this->assertArrayHasKey('shopee', $breakdown);

        // TikTok totals: 45.000 + 20.000 + 5.000 = 70.000
        $this->assertEquals(45000, $breakdown['tiktok']['live_salary']);
        $this->assertEquals(20000, $breakdown['tiktok']['content_edit_salary']);
        $this->assertEquals(5000, $breakdown['tiktok']['content_live_salary']);
        $this->assertEquals(70000, $breakdown['tiktok']['total_salary']);
        $this->assertEquals(50, $breakdown['tiktok']['target_hours']);
        $this->assertFalse($breakdown['tiktok']['target_met']);

        // Shopee totals: 50.000 + 12.000 + 12.000 = 74.000
        $this->assertEquals(50000, $breakdown['shopee']['live_salary']);
        $this->assertEquals(12000, $breakdown['shopee']['content_edit_salary']);
        $this->assertEquals(12000, $breakdown['shopee']['content_live_salary']);
        $this->assertEquals(74000, $breakdown['shopee']['total_salary']);
        $this->assertEquals(30, $breakdown['shopee']['target_hours']);
        $this->assertFalse($breakdown['shopee']['target_met']);

        // Combined Total
        $totalAll = collect($breakdown)->sum('total_salary');
        $this->assertEquals(144000, $totalAll);
    }

    public function test_user_can_submit_multiple_platforms_on_same_session(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $this->actingAs($user);

        $today = now()->toDateString();

        // Submit attendance for TikTok
        $responseTikTok = $this->post(route('user.attendances.store'), [
            'attendance_date' => $today,
            'session_name' => 'Sesi 1',
            'platform' => 'tiktok',
            'live_duration_hours' => 3.0,
            'content_edit_count' => 0,
            'content_live_count' => 0,
            'sales_count' => 10,
        ]);
        $responseTikTok->assertRedirect(route('user.attendances.index'));
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'attendance_date' => $today . ' 00:00:00',
            'session_name' => 'Sesi 1',
            'platform' => 'tiktok',
            'live_duration_minutes' => 180,
        ]);

        // Submit attendance for Shopee on the SAME date & session
        $responseShopee = $this->post(route('user.attendances.store'), [
            'attendance_date' => $today,
            'session_name' => 'Sesi 1',
            'platform' => 'shopee',
            'live_duration_hours' => 2.0,
            'content_edit_count' => 0,
            'content_live_count' => 0,
            'sales_count' => 5,
        ]);
        $responseShopee->assertRedirect(route('user.attendances.index'));
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'attendance_date' => $today . ' 00:00:00',
            'session_name' => 'Sesi 1',
            'platform' => 'shopee',
            'live_duration_minutes' => 120,
        ]);

        $this->assertEquals(2, Attendance::where('user_id', $user->id)->count());
    }

    public function test_user_cannot_submit_duplicate_same_platform_on_same_session(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $this->actingAs($user);

        $today = now()->toDateString();

        // First submit for TikTok Sesi 1
        $this->post(route('user.attendances.store'), [
            'attendance_date' => $today,
            'session_name' => 'Sesi 1',
            'platform' => 'tiktok',
            'live_duration_hours' => 3.0,
            'sales_count' => 10,
        ]);

        // Duplicate submit for TikTok Sesi 1 on same date
        $responseDuplicate = $this->post(route('user.attendances.store'), [
            'attendance_date' => $today,
            'session_name' => 'Sesi 1',
            'platform' => 'tiktok',
            'live_duration_hours' => 1.0,
            'sales_count' => 5,
        ]);

        $responseDuplicate->assertSessionHas('error');
        $this->assertEquals(1, Attendance::where('user_id', $user->id)->count());
    }

    public function test_user_can_submit_attendance_without_session_name(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $this->actingAs($user);

        $today = now()->toDateString();

        // Submit 1st attendance without session_name
        $response1 = $this->post(route('user.attendances.store'), [
            'action' => 'save',
            'attendance_date' => $today,
            'platform' => 'tiktok',
            'live_duration_hours' => 2.0,
            'sales_count' => 10,
        ]);

        $response1->assertRedirect(route('user.attendances.index'));
        $att1 = Attendance::where('user_id', $user->id)->where('platform', 'tiktok')->first();
        $this->assertNotNull($att1);
        $this->assertEquals('Sesi 1', $att1->session_name);

        // Submit 2nd attendance for same day & platform without session_name -> auto-assigns Sesi 2
        $response2 = $this->post(route('user.attendances.store'), [
            'action' => 'save',
            'attendance_date' => $today,
            'platform' => 'tiktok',
            'live_duration_hours' => 1.5,
            'sales_count' => 5,
        ]);

        $response2->assertRedirect(route('user.attendances.index'));
        $this->assertEquals(2, Attendance::where('user_id', $user->id)->where('platform', 'tiktok')->count());
        $att2 = Attendance::where('user_id', $user->id)->where('platform', 'tiktok')->orderByDesc('id')->first();
        $this->assertEquals('Sesi 2', $att2->session_name);
    }

    public function test_save_and_next_action_redirects_with_params(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $this->actingAs($user);

        $today = now()->toDateString();

        $response = $this->post(route('user.attendances.store'), [
            'action' => 'save_and_next',
            'attendance_date' => $today,
            'session_name' => 'Sesi 2',
            'platform' => 'tiktok',
            'live_duration_hours' => 3.0,
            'sales_count' => 15,
        ]);

        $response->assertRedirect(route('user.attendances.create', [
            'platform' => 'shopee',
            'date' => $today,
            'session' => 'Sesi 2',
        ]));
    }

    public function test_admin_can_update_salary_scheme_with_custom_mode_all_rates_and_targets(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $scheme = SalaryScheme::create([
            'user_id' => $user->id,
            'hourly_rate' => 20000,
            'content_edit_rate' => 10000,
            'content_live_rate' => 5000,
            'monthly_target_hours' => 80,
        ]);

        $this->actingAs($admin);

        $response = $this->put(route('admin.salary-schemes.update', $user), [
            'rate_mode' => 'custom',
            'hourly_rate' => 18000,
            'content_edit_rate' => 10000,
            'content_live_rate' => 5000,
            'platform_rates' => [
                'tiktok' => [
                    'hourly_rate' => 18000,
                    'content_edit_rate' => 9000,
                    'content_live_rate' => 4500,
                    'target_hours' => 50,
                ],
                'shopee' => [
                    'hourly_rate' => 22000,
                    'content_edit_rate' => 12000,
                    'content_live_rate' => 6000,
                    'target_hours' => 30,
                ],
            ],
            'bonus_pcs_threshold' => 20,
            'bonus_amount' => 10000,
        ]);

        $response->assertRedirect(route('admin.salary-schemes.index'));

        $scheme->refresh();
        $this->assertEquals(18000, $scheme->getHourlyRateForPlatform('tiktok'));
        $this->assertEquals(9000, $scheme->getContentEditRateForPlatform('tiktok'));
        $this->assertEquals(4500, $scheme->getContentLiveRateForPlatform('tiktok'));
        $this->assertEquals(50, $scheme->getTargetHoursForPlatform('tiktok'));

        $this->assertEquals(22000, $scheme->getHourlyRateForPlatform('shopee'));
        $this->assertEquals(12000, $scheme->getContentEditRateForPlatform('shopee'));
        $this->assertEquals(6000, $scheme->getContentLiveRateForPlatform('shopee'));
        $this->assertEquals(30, $scheme->getTargetHoursForPlatform('shopee'));

        // Combined monthly target hours is sum of active platforms: 50 + 30 = 80
        $this->assertEquals(80, (float) $scheme->monthly_target_hours);
    }

    public function test_admin_can_update_salary_scheme_with_same_mode_for_all(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $scheme = SalaryScheme::create([
            'user_id' => $user->id,
            'hourly_rate' => 20000,
            'content_edit_rate' => 10000,
            'content_live_rate' => 5000,
        ]);

        $this->actingAs($admin);

        $response = $this->put(route('admin.salary-schemes.update', $user), [
            'rate_mode' => 'same',
            'monthly_target_hours' => 80,
            'hourly_rate' => 25000,
            'content_edit_rate' => 15000,
            'content_live_rate' => 8000,
            'bonus_pcs_threshold' => 20,
            'bonus_amount' => 10000,
        ]);

        $response->assertRedirect(route('admin.salary-schemes.index'));

        $scheme->refresh();
        // In same mode, both TikTok and Shopee get 25.000, 15.000, 8.000 and target 80 equally!
        $this->assertEquals(25000, $scheme->getHourlyRateForPlatform('tiktok'));
        $this->assertEquals(15000, $scheme->getContentEditRateForPlatform('tiktok'));
        $this->assertEquals(8000, $scheme->getContentLiveRateForPlatform('tiktok'));
        $this->assertEquals(80, $scheme->getTargetHoursForPlatform('tiktok'));

        $this->assertEquals(25000, $scheme->getHourlyRateForPlatform('shopee'));
        $this->assertEquals(15000, $scheme->getContentEditRateForPlatform('shopee'));
        $this->assertEquals(8000, $scheme->getContentLiveRateForPlatform('shopee'));
        $this->assertEquals(80, $scheme->getTargetHoursForPlatform('shopee'));
        $this->assertEquals(80, (float) $scheme->monthly_target_hours);
    }

    public function test_admin_can_view_salary_scheme_edit_page_without_copy_buttons(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.salary-schemes.edit', $user));
        $response->assertStatus(200);

        // Asserts copy buttons are completely removed
        $response->assertDontSee('Salin Nilai');
        $response->assertDontSee('Samakan dari');
        $response->assertDontSee('copyRatesToAll');
        $response->assertDontSee('copySingleFrom');

        // Asserts target hours is present in marketplace inputs
        $response->assertSee('Target Live Bulanan');
    }

    public function test_admin_can_update_marketplace_rate_mode_in_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        // 1. Update to 'same' mode with new uniform rate
        $response = $this->put(route('admin.settings.update-system'), [
            'setting_group' => 'marketplaces',
            'marketplace_rate_mode' => 'same',
            'default_hourly_rate' => 22000,
            'marketplaces' => [
                'tiktok' => ['enabled' => '1'],
                'shopee' => ['enabled' => '1'],
            ],
        ]);
        $response->assertRedirect();
        $this->assertEquals('same', Setting::getValue('marketplace_rate_mode'));
        $this->assertEquals(22000, (int) Setting::getValue('default_hourly_rate'));

        // 2. Update to 'different' mode with per-marketplace rates
        $response = $this->put(route('admin.settings.update-system'), [
            'setting_group' => 'marketplaces',
            'marketplace_rate_mode' => 'different',
            'marketplaces' => [
                'tiktok' => ['enabled' => '1', 'default_hourly_rate' => 20000],
                'shopee' => ['enabled' => '1', 'default_hourly_rate' => 25000],
            ],
        ]);
        $response->assertRedirect();
        $this->assertEquals('different', Setting::getValue('marketplace_rate_mode'));

        $mps = Setting::getMarketplaces();
        $this->assertEquals(20000, $mps['tiktok']['default_hourly_rate']);
        $this->assertEquals(25000, $mps['shopee']['default_hourly_rate']);
    }

    public function test_salary_scheme_views_respect_marketplace_rate_mode(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        SalaryScheme::create([
            'user_id' => $user->id,
            'hourly_rate' => 20000,
            'content_edit_rate' => 10000,
            'content_live_rate' => 5000,
            'monthly_target_hours' => 80,
            'platform_rates' => [
                'tiktok' => ['hourly_rate' => 20000, 'content_edit_rate' => 10000, 'content_live_rate' => 5000, 'target_hours' => 40],
                'shopee' => ['hourly_rate' => 25000, 'content_edit_rate' => 12000, 'content_live_rate' => 6000, 'target_hours' => 40],
            ],
        ]);

        $this->actingAs($admin);

        // When mode is 'same'
        Setting::setValue('marketplace_rate_mode', 'same');
        $indexResponse = $this->get(route('admin.salary-schemes.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('/ jam');

        $editResponse = $this->get(route('admin.salary-schemes.edit', $user));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Model Upah Seragam');

        // When mode is 'different'
        Setting::setValue('marketplace_rate_mode', 'different');
        $editResponseDiff = $this->get(route('admin.salary-schemes.edit', $user));
        $editResponseDiff->assertStatus(200);
        $editResponseDiff->assertSee('Model Upah Beda Marketplace');
    }

    public function test_unfilled_salary_scheme_fields_follow_global_settings(): void
    {
        // 1. Configure global settings
        Setting::setValue('default_hourly_rate', 25000);
        Setting::setValue('content_edit_rate', 14000);
        Setting::setValue('content_live_rate', 7000);
        Setting::setValue('monthly_target_hours', 70);

        // 2. Create user with null salary scheme values (unfilled)
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $scheme = SalaryScheme::create([
            'user_id' => $user->id,
            'hourly_rate' => null,
            'content_edit_rate' => null,
            'content_live_rate' => null,
            'monthly_target_hours' => null,
            'platform_rates' => null,
        ]);

        // Asserts: Unfilled fields follow global settings
        $this->assertEquals(25000, $scheme->getHourlyRateForPlatform('tiktok'));
        $this->assertEquals(14000, $scheme->getContentEditRateForPlatform('tiktok'));
        $this->assertEquals(7000, $scheme->getContentLiveRateForPlatform('tiktok'));
        $this->assertEquals(70.0, $scheme->getTargetHoursForPlatform('tiktok'));

        // 3. User fills in a custom hourly rate (35000) but leaves others unfilled
        $scheme->update([
            'hourly_rate' => 35000,
        ]);
        $scheme->refresh();

        // Custom filled rate is used, others still follow global settings!
        $this->assertEquals(35000, $scheme->getHourlyRateForPlatform('tiktok'));
        $this->assertEquals(14000, $scheme->getContentEditRateForPlatform('tiktok'));
        $this->assertEquals(7000, $scheme->getContentLiveRateForPlatform('tiktok'));
        $this->assertEquals(70.0, $scheme->getTargetHoursForPlatform('tiktok'));
    }

    public function test_admin_can_update_content_rates_and_target_in_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        $response = $this->put(route('admin.settings.update-system'), [
            'setting_group' => 'marketplaces',
            'marketplace_rate_mode' => 'same',
            'default_hourly_rate' => 20000,
            'content_edit_rate' => 12000,
            'content_live_rate' => 6000,
            'monthly_target_hours' => 75,
            'marketplaces' => [
                'tiktok' => ['enabled' => '1'],
                'shopee' => ['enabled' => '1'],
            ],
        ]);
        $response->assertRedirect();

        $this->assertEquals(12000, (int) Setting::getValue('content_edit_rate'));
        $this->assertEquals(6000, (int) Setting::getValue('content_live_rate'));
        $this->assertEquals(75.0, (float) Setting::getValue('monthly_target_hours'));
    }

    public function test_admin_can_clear_salary_scheme_fields_to_revert_to_global_defaults(): void
    {
        Setting::setValue('default_hourly_rate', 20000);
        Setting::setValue('content_edit_rate', 10000);
        Setting::setValue('content_live_rate', 5000);
        Setting::setValue('monthly_target_hours', 80);

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $scheme = SalaryScheme::create([
            'user_id' => $user->id,
            'hourly_rate' => 30000,
            'content_edit_rate' => 15000,
            'content_live_rate' => 8000,
            'monthly_target_hours' => 90,
        ]);

        $this->actingAs($admin);

        // Submit form with empty strings (cleared fields)
        $response = $this->put(route('admin.salary-schemes.update', $user), [
            'rate_mode' => 'same',
            'hourly_rate' => '',
            'content_edit_rate' => '',
            'content_live_rate' => '',
            'monthly_target_hours' => '',
        ]);

        $response->assertRedirect(route('admin.salary-schemes.index'));

        $scheme->refresh();
        $this->assertNull($scheme->hourly_rate);
        $this->assertNull($scheme->content_edit_rate);
        $this->assertNull($scheme->content_live_rate);
        $this->assertNull($scheme->monthly_target_hours);

        // They now fall back to the global defaults!
        $this->assertEquals(20000, $scheme->getHourlyRateForPlatform('tiktok'));
        $this->assertEquals(10000, $scheme->getContentEditRateForPlatform('tiktok'));
        $this->assertEquals(5000, $scheme->getContentLiveRateForPlatform('tiktok'));
        $this->assertEquals(80.0, $scheme->getTargetHoursForPlatform('tiktok'));
    }
}
