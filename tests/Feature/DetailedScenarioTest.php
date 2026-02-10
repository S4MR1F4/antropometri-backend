<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use App\Models\Measurement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class DetailedScenarioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Balita Workflow (Create Subject -> Measure -> Check Stunting/Wasting).
     */
    public function test_petugas_can_manage_balita_and_get_calculation()
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        Sanctum::actingAs($petugas);

        // 1. Create Balita Subject (1 year old)
        $dob = now()->subYear()->format('Y-m-d');
        $subjectResponse = $this->postJson('/api/subjects', [
            'name' => 'Bayi Sehat',
            'date_of_birth' => $dob,
            'gender' => 'L',
            'parent_name' => 'Ibu Budi',
            'address' => 'Jl. Mawar',
            'nik' => '3201012020200001',
        ]);

        $subjectResponse->assertStatus(201);
        $subjectId = $subjectResponse->json('data.subject.id');

        // 2. Submit Measurement (Normal)
        // 1 year old male: Median Height ~75.7cm, Weight ~9.6kg
        $measurementResponse = $this->postJson("/api/subjects/{$subjectId}/measurements", [
            'height' => 76,
            'weight' => 10,
            'head_circumference' => 46,
            'measurement_type' => 'berbaring',
            'measurement_date' => now()->format('Y-m-d'),
        ]);

        $measurementResponse->assertStatus(201)
            ->assertJsonPath('data.measurement.category', 'balita')
            // Check existence of calculated fields
            ->assertJsonStructure([
                'data' => [
                    'measurement' => [
                        'result' => [
                            'status_bbu',
                            'status_tbu',
                            'status_bbtb'
                        ]
                    ]
                ]
            ]);

        // 3. Verify specific logic (Simplistic check if status is returning something valid)
        $results = $measurementResponse->json('data.measurement.result');
        $this->assertNotNull($results['status_bbu']);
        $this->assertNotNull($results['status_tbu']);
    }

    /**
     * Test Remaja Workflow (Create Subject -> Measure -> Check BMI/U).
     */
    public function test_petugas_can_manage_remaja_and_get_calculation()
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        Sanctum::actingAs($petugas);

        // 1. Create Remaja Subject (15 years old)
        $dob = now()->subYears(15)->format('Y-m-d');
        $subject = Subject::factory()->create([
            'user_id' => $petugas->id,
            'date_of_birth' => $dob,
            'gender' => 'P', // Female
            'name' => 'Remaja Putri'
        ]);

        // 2. Submit Measurement
        $measurementResponse = $this->postJson("/api/subjects/{$subject->id}/measurements", [
            'height' => 160,
            'weight' => 50,
            'measurement_date' => now()->format('Y-m-d'),
        ]);

        $measurementResponse->assertStatus(201)
            ->assertJsonPath('data.measurement.category', 'remaja')
            ->assertJsonStructure([
                'data' => [
                    'measurement' => [
                        'result' => [ // Changed from results => result
                            'bmi',
                            'zscore_imtu',
                            'status_imtu'
                        ]
                    ]
                ]
            ]);
    }

    /**
     * Test Pagination and Filtering logic for History.
     */
    public function test_history_endpoint_pagination_and_filtering()
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        Sanctum::actingAs($petugas);

        $subject = Subject::factory()->create(['user_id' => $petugas->id]);

        // Use factories to create data reliably
        Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $petugas->id,
            'measurement_date' => now()->subDays(2),
            'height' => 100,
            'weight' => 15,
        ]);
        Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $petugas->id,
            'measurement_date' => now()->subDay(),
            'height' => 101,
            'weight' => 16,
        ]);
        Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $petugas->id,
            'measurement_date' => now(),
            'height' => 102,
            'weight' => 17,
        ]);

        // Get History
        $response = $this->getJson('/api/measurements?per_page=2');

        $response->assertStatus(200)
            ->assertJsonPath('data.pagination.total', 3)
            ->assertJsonPath('data.pagination.per_page', 2)
            ->assertJsonPath('data.pagination.current_page', 1);

        // Default sort should be latest first
        $data = $response->json('data.measurements');
        $this->assertEquals(102, $data[0]['height']); // Day 3
        $this->assertEquals(101, $data[1]['height']); // Day 2
    }

    /**
     * Test Validation Rules.
     */
    public function test_negative_values_are_rejected()
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        Sanctum::actingAs($petugas);
        $subject = Subject::factory()->create(['user_id' => $petugas->id]);

        $response = $this->postJson("/api/subjects/{$subject->id}/measurements", [
            'height' => -10,
            'weight' => 10,
            'measurement_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['height']);
    }
}
