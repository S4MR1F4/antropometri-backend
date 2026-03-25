<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MeasurementFlowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Insert dummy reference data to prevent calculation errors
        \Illuminate\Support\Facades\DB::table('reference_balita_bbu')->insert([
            'gender' => 'L', 'age_months' => 12, 'neg3sd' => 7.0, 'neg2sd' => 8.0, 'neg1sd' => 9.0,
            'median' => 10.0, 'pos1sd' => 11.0, 'pos2sd' => 12.0, 'pos3sd' => 13.0,
        ]);
        \Illuminate\Support\Facades\DB::table('reference_balita_tbu')->insert([
            'gender' => 'L', 'age_months' => 12, 'neg3sd' => 65.0, 'neg2sd' => 68.0, 'neg1sd' => 70.0,
            'median' => 75.0, 'pos1sd' => 78.0, 'pos2sd' => 80.0, 'pos3sd' => 82.0,
        ]);
        \Illuminate\Support\Facades\DB::table('reference_balita_bbtb')->insert([
            'gender' => 'L', 'height' => 75.0, 'neg3sd' => 7.0, 'neg2sd' => 8.0, 'neg1sd' => 9.0,
            'median' => 10.0, 'pos1sd' => 11.0, 'pos2sd' => 12.0, 'pos3sd' => 13.0,
        ]);
    }

    public function test_can_create_balita_measurement()
    {
        $user = User::factory()->create();
        $subject = Subject::factory()->create([
            'date_of_birth' => now()->subMonths(12)->format('Y-m-d'), // 12 months = balita
            'gender' => 'L',
            'user_id' => $user->id,
        ]);

        $payload = [
            'measurement_date' => now()->format('Y-m-d'),
            'weight' => 9.5,
            'height' => 75.0,
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/subjects/{$subject->id}/measurements", $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'measurement' => [
                             'id',
                             'weight',
                             'height',
                             'category',
                             'result' => [
                                 'status_bbu',
                                 'status_tbu',
                                 'status_bbtb',
                             ]
                         ]
                     ]
                 ]);
                 
        $this->assertDatabaseHas('measurements', [
            'subject_id' => $subject->id,
            'weight' => 9.5,
            'category' => 'balita'
        ]);
    }

    public function test_can_get_subject_measurements_with_trend()
    {
        $user = User::factory()->create();
        $subject = Subject::factory()->create([
            'date_of_birth' => now()->subYears(20)->format('Y-m-d'), // Dewasa
            'user_id' => $user->id,
        ]);

        Measurement::factory()->count(3)->create([
            'subject_id' => $subject->id,
            'user_id' => $user->id,
            'category' => 'dewasa',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/subjects/{$subject->id}/measurements");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'subject' => [
                             'id',
                             'name',
                             'category'
                         ],
                         'measurements',
                         'pagination'
                     ]
                 ]);
                 
        $this->assertCount(3, $response->json('data.measurements'));
    }

    public function test_can_get_history_list()
    {
        $user = User::factory()->create();
        $subject = Subject::factory()->create(['user_id' => $user->id]);
        
        Measurement::factory()->count(5)->create([
            'subject_id' => $subject->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/measurements");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'measurements',
                         'pagination'
                     ]
                 ]);
                 
        $this->assertCount(5, $response->json('data.measurements'));
    }

    public function test_can_get_grouped_history()
    {
        $user = User::factory()->create();
        
        $subject1 = Subject::factory()->create(['user_id' => $user->id]);
        $subject2 = Subject::factory()->create(['user_id' => $user->id]);
        
        Measurement::factory()->create(['subject_id' => $subject1->id, 'user_id' => $user->id]);
        Measurement::factory()->create(['subject_id' => $subject1->id, 'user_id' => $user->id]);
        Measurement::factory()->create(['subject_id' => $subject2->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/measurements/grouped");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'subjects',
                         'pagination'
                     ]
                 ]);
                 
        // Grouped by subjects, should return 2 records since there are 2 subjects
        $this->assertCount(2, $response->json('data.subjects'));
    }

    public function test_can_delete_measurement()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $subject = Subject::factory()->create(['user_id' => $user->id]);
        $measurement = Measurement::factory()->create([
            'subject_id' => $subject->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/measurements/{$measurement->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Pengukuran berhasil dihapus',
                 ]);

        $this->assertDatabaseMissing('measurements', [
            'id' => $measurement->id,
            'deleted_at' => null // Using soft deletes probably, or actually checking its absence
        ]);
    }

    public function test_can_sync_measurements_offline()
    {
        $user = User::factory()->create();
        $subject = Subject::factory()->create([
            'date_of_birth' => now()->subMonths(10)->format('Y-m-d'),
            'user_id' => $user->id,
        ]);

        // Mock payload mimicking offline sync
        $payload = [
            'records' => [
                [
                    'local_id' => 'uuid-1234',
                    'subject_id' => $subject->id,
                    'measurement_date' => now()->format('Y-m-d'),
                    'created_at_local' => now()->toISOString(),
                    'hash' => 'dummy_hash',
                    'weight' => 8.0,
                    'height' => 70.0,
                    'category' => 'balita',
                ],
                [
                    'local_id' => 'uuid-5678',
                    'subject_id' => $subject->id,
                    'measurement_date' => now()->subDays(30)->format('Y-m-d'),
                    'created_at_local' => now()->subDays(30)->toISOString(),
                    'hash' => 'dummy_hash_2',
                    'weight' => 7.5,
                    'height' => 68.0,
                    'category' => 'balita',
                ]
            ]
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/sync/measurements', $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'batch_id',
                         'total',
                         'synced',
                         'skipped',
                         'conflicts'
                     ]
                 ]);

        $this->assertEquals(2, $response->json('data.synced'));
        $this->assertDatabaseCount('measurements', 2);
        $this->assertDatabaseHas('measurements', ['weight' => 8.0]);
        $this->assertDatabaseHas('measurements', ['weight' => 7.5]);
    }
}
