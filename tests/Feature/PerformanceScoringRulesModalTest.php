<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceScoringRulesModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_performance_page_with_scoring_rules_modal(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        $response = $this->get(route('admin.performance.index'));
        $response->assertStatus(200);

        // Asserts button exists
        $response->assertSee('Aturan Skor');
        $response->assertSee('data-modal="scoringRulesModal"', false);

        // Asserts modal elements and scoring explanation exist
        $response->assertSee('id="scoringRulesModal"', false);
        $response->assertSee('Aturan Penilaian Skor Host');
        $response->assertSee('Penjualan × 40%');
        $response->assertSee('Durasi Live × 35%');
        $response->assertSee('Konten × 25%');
        $response->assertSee('Diamond Host');
        $response->assertSee('Gold Star');
        $response->assertSee('Silver Pro');
        $response->assertSee('Bronze Host');
        $response->assertSee('Rising Star');
    }

    public function test_user_can_view_performance_page_with_scoring_rules_modal(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $this->actingAs($user);

        $response = $this->get(route('user.performance.index'));
        $response->assertStatus(200);

        // Asserts button exists in user view
        $response->assertSee('Aturan Skor');
        $response->assertSee('data-modal="scoringRulesModal"', false);

        // Asserts modal elements and scoring explanation exist
        $response->assertSee('id="scoringRulesModal"', false);
        $response->assertSee('Aturan Penilaian Skor Host');
        $response->assertSee('Penjualan × 40%');
        $response->assertSee('Durasi Live × 35%');
        $response->assertSee('Konten × 25%');
        $response->assertSee('Diamond Host');
        $response->assertSee('Mengerti & Tutup');
    }
}
