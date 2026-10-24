<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollaborationTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_staff_can_view_team_collaboration_leaderboard_and_stats(): void
    {
        $admin = User::factory()->create(['name' => 'Admin Utama', 'role' => 'admin']);
        $petugas1 = User::factory()->create(['name' => 'Abang Maman', 'role' => 'petugas']);
        $petugas2 = User::factory()->create(['name' => 'Siti Nurhaliza', 'role' => 'petugas']);

        $subject1 = Subject::factory()->create(['user_id' => $petugas1->id]);
        $subject2 = Subject::factory()->create(['user_id' => $petugas2->id]);

        // Petugas 1 mencatat 2 pemeriksaan
        Measurement::factory()->count(2)->create([
            'subject_id' => $subject1->id,
            'user_id' => $petugas1->id,
            'measurement_date' => now()->toDateString(),
        ]);

        // Petugas 2 mencatat 3 pemeriksaan
        Measurement::factory()->count(3)->create([
            'subject_id' => $subject2->id,
            'user_id' => $petugas2->id,
            'measurement_date' => now()->toDateString(),
        ]);

        // Petugas 1 mengakses endpoint kolaborasi tim
        $response = $this->actingAs($petugas1)->getJson('/api/collaboration/stats');

        $response->assertStatus(200)
            ->assertJsonPath('data.total_system', 5)
            ->assertJsonPath('data.online_server', 5);

        $contributions = $response->json('data.contributions');
        $this->assertCount(2, $contributions);
        
        // Petugas 2 di urutan pertama (3 data = 60%)
        $this->assertEquals('Siti Nurhaliza', $contributions[0]['name']);
        $this->assertEquals(3, $contributions[0]['count']);
        $this->assertEquals(60.0, $contributions[0]['percentage']);

        // Petugas 1 di urutan kedua (2 data = 40%)
        $this->assertEquals('Abang Maman', $contributions[1]['name']);
        $this->assertEquals(2, $contributions[1]['count']);
        $this->assertEquals(40.0, $contributions[1]['percentage']);
    }

    public function test_staff_can_view_measurement_detail_recorded_by_other_staff(): void
    {
        $petugas1 = User::factory()->create(['role' => 'petugas']);
        $petugas2 = User::factory()->create(['role' => 'petugas']);

        $subject = Subject::factory()->create(['user_id' => $petugas2->id]);
        $measurement = Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $petugas2->id,
        ]);

        // Petugas 1 membuka detail pemeriksaan milik Petugas 2 (misal dari Notifikasi)
        $response = $this->actingAs($petugas1)->getJson("/api/measurements/{$measurement->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.measurement.id', $measurement->id);
    }

    public function test_staff_cannot_update_or_delete_measurement_recorded_by_other_staff(): void
    {
        $petugas1 = User::factory()->create(['role' => 'petugas']);
        $petugas2 = User::factory()->create(['role' => 'petugas']);

        $subject = Subject::factory()->create(['user_id' => $petugas2->id]);
        $measurement = Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $petugas2->id,
            'weight' => 12.0,
        ]);

        // Petugas 1 mencoba update pemeriksaan milik Petugas 2
        $responseUpdate = $this->actingAs($petugas1)->putJson("/api/measurements/{$measurement->id}", [
            'measurement_date' => now()->toDateString(),
            'weight' => 14.0,
            'height' => 85.0,
        ]);
        $responseUpdate->assertStatus(403);

        // Petugas 1 mencoba delete pemeriksaan milik Petugas 2
        $responseDelete = $this->actingAs($petugas1)->deleteJson("/api/measurements/{$measurement->id}");
        $responseDelete->assertStatus(403);
    }
}
