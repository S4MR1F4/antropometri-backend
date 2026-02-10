<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
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

    public function test_admin_can_search_users_by_name()
    {
        User::factory()->create(['name' => 'Salman Alfarisi', 'email' => 'salman@example.com']);
        User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/users?search=Salman');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.users')
            ->assertJsonPath('data.users.0.name', 'Salman Alfarisi');
    }

    public function test_admin_can_search_users_by_email()
    {
        User::factory()->create(['name' => 'User One', 'email' => 'unique_one@example.com']);
        User::factory()->create(['name' => 'User Two', 'email' => 'unique_two@example.com']);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/users?search=unique_one');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.users')
            ->assertJsonPath('data.users.0.email', 'unique_one@example.com');
    }

    public function test_can_search_measurements_by_patient_name()
    {
        $subject1 = Subject::factory()->create(['name' => 'Anak Salman', 'user_id' => $this->petugas->id]);
        $subject2 = Subject::factory()->create(['name' => 'Anak Budi', 'user_id' => $this->petugas->id]);

        Measurement::factory()->create([
            'subject_id' => $subject1->id,
            'user_id' => $this->petugas->id,
            'measurement_date' => now()
        ]);
        Measurement::factory()->create([
            'subject_id' => $subject2->id,
            'user_id' => $this->petugas->id,
            'measurement_date' => now()
        ]);

        $response = $this->actingAs($this->petugas)
            ->getJson('/api/measurements?search=Salman');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.measurements')
            ->assertJsonPath('data.measurements.0.subject.name', 'Anak Salman');
    }

    public function test_admin_can_search_measurements_by_staff_name()
    {
        $staff1 = User::factory()->create(['name' => 'Petugas Salman']);
        $staff2 = User::factory()->create(['name' => 'Petugas Budi']);

        $subject = Subject::factory()->create();

        Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $staff1->id,
            'measurement_date' => now()
        ]);
        Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $staff2->id,
            'measurement_date' => now()
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/measurements?search=Salman');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.measurements')
            ->assertJsonPath('data.measurements.0.user.name', 'Petugas Salman');
    }

    public function test_search_results_are_case_insensitive()
    {
        Subject::factory()->create(['name' => 'SALMAN ALFARISI', 'user_id' => $this->petugas->id]);

        Measurement::factory()->create([
            'subject_id' => Subject::first()->id,
            'user_id' => $this->petugas->id,
            'measurement_date' => now()
        ]);

        $response = $this->actingAs($this->petugas)
            ->getJson('/api/measurements?search=salman');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.measurements');
    }
}
