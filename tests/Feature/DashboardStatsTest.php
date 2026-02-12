<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->petugas = User::factory()->create(['role' => 'petugas']);
    }

    public function test_admin_can_get_filtered_stats()
    {
        // 1. Create a subject
        $subject = Subject::factory()->create();

        // 2. Create measurements in different periods
        // Today
        Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $this->petugas->id,
            'measurement_date' => now()->toDateString(),
            'category' => 'balita',
            'status_bbtb' => 'Gizi Baik'
        ]);

        // Last Week
        Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $this->petugas->id,
            'measurement_date' => now()->subtract('7 days')->toDateString(),
            'category' => 'balita',
            'status_bbtb' => 'Gizi Baik'
        ]);

        // Test 1: Daily Filter
        $response = $this->actingAs($this->admin)->getJson('/api/admin/statistics?from_date=' . now()->toDateString());
        $response->assertStatus(200)
            ->assertJsonPath('data.total_measurements', 1);

        // Test 2: Weekly Filter
        $response = $this->actingAs($this->admin)->getJson('/api/admin/statistics?from_date=' . now()->subtract('7 days')->toDateString());
        $response->assertStatus(200)
            ->assertJsonPath('data.total_measurements', 2);
    }

    public function test_petugas_can_get_own_filtered_stats()
    {
        $otherPetugas = User::factory()->create(['role' => 'petugas']);
        $subject = Subject::factory()->create();

        // Me
        Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $this->petugas->id,
            'measurement_date' => now()->toDateString(),
        ]);

        // Other
        Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $otherPetugas->id,
            'measurement_date' => now()->toDateString(),
        ]);

        // Fetch via /auth/me with filters
        $response = $this->actingAs($this->petugas)->getJson('/api/auth/me?from_date=' . now()->toDateString());

        $response->assertStatus(200)
            ->assertJsonPath('data.user.stats.total_measurements', 1);
    }

    public function test_health_distribution_is_correct()
    {
        $subject1 = Subject::factory()->create();
        $subject2 = Subject::factory()->create();

        // Patient 1: Normal
        Measurement::factory()->create([
            'subject_id' => $subject1->id,
            'status_bbtb' => 'Gizi Baik',
            'status_tbu' => 'Normal',
            'category' => 'balita'
        ]);

        // Patient 2: Stunting
        Measurement::factory()->create([
            'subject_id' => $subject2->id,
            'status_bbtb' => 'Gizi Baik',
            'status_tbu' => 'Sangat Pendek',
            'category' => 'balita'
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/admin/statistics');

        $response->assertStatus(200)
            ->assertJsonPath('data.by_status.normal_count', 1)
            ->assertJsonPath('data.by_status.stunting_count', 1);
    }
}
