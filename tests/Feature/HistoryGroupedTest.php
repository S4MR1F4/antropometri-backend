<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryGroupedTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_grouped_history()
    {
        $user = User::factory()->create(['role' => 'petugas']);
        $subject1 = Subject::factory()->create(['user_id' => $user->id, 'name' => 'Patient A']);
        $subject2 = Subject::factory()->create(['user_id' => $user->id, 'name' => 'Patient B']);

        // 3 measurements for A, 1 for B
        Measurement::factory()->count(3)->create([
            'subject_id' => $subject1->id,
            'user_id' => $user->id,
            'category' => 'dewasa',
            'measurement_date' => now()->subDays(1)
        ]);

        Measurement::factory()->create([
            'subject_id' => $subject2->id,
            'user_id' => $user->id,
            'category' => 'dewasa',
            'measurement_date' => now()
        ]);

        $response = $this->actingAs($user)->getJson('/api/measurements/grouped');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.subjects')
            ->assertJsonPath('data.subjects.0.name', 'Patient B') // Latest first
            ->assertJsonPath('data.subjects.0.measurement_count', 1)
            ->assertJsonPath('data.subjects.1.name', 'Patient A')
            ->assertJsonPath('data.subjects.1.measurement_count', 3);
    }

    public function test_grouped_history_search()
    {
        $user = User::factory()->create(['role' => 'petugas']);
        $subject1 = Subject::factory()->create(['user_id' => $user->id, 'name' => 'UniqueName']);
        $subject2 = Subject::factory()->create(['user_id' => $user->id, 'name' => 'OtherName']);

        Measurement::factory()->create(['subject_id' => $subject1->id, 'user_id' => $user->id]);
        Measurement::factory()->create(['subject_id' => $subject2->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/measurements/grouped?search=Unique');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.subjects')
            ->assertJsonPath('data.subjects.0.name', 'UniqueName');
    }
}
