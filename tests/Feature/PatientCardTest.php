<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_patient_card_with_history(): void
    {
        $user = User::factory()->create(['role' => 'petugas']);

        $subject = Subject::create([
            'user_id' => $user->id,
            'name' => 'Ananda Pratama',
            'normalized_name' => 'ANANDA PRATAMA',
            'date_of_birth' => '2023-01-15',
            'gender' => 'L',
            'parent_name' => 'Bapak Pratama',
        ]);

        $m1 = Measurement::create([
            'subject_id' => $subject->id,
            'user_id' => $user->id,
            'measurement_date' => '2024-01-15',
            'category' => 'balita',
            'age_in_months' => 12,
            'age_in_years' => 1,
            'weight' => 9.5,
            'height' => 75.0,
            'status_bbu' => 'Gizi Baik',
            'status_tbu' => 'Normal',
            'status_bbtb' => 'Gizi Baik',
        ]);

        $m2 = Measurement::create([
            'subject_id' => $subject->id,
            'user_id' => $user->id,
            'measurement_date' => '2024-06-15',
            'category' => 'balita',
            'age_in_months' => 17,
            'age_in_years' => 1,
            'weight' => 11.2,
            'height' => 82.0,
            'status_bbu' => 'Gizi Baik',
            'status_tbu' => 'Normal',
            'status_bbtb' => 'Gizi Baik',
            'recommendation' => 'Pertahankan asupan gizi seimbang',
        ]);

        $response = $this->get('/card/patient/' . $subject->id);

        $response->assertStatus(200);
        $response->assertSee('Ananda Pratama');
        $response->assertSee('Bapak Pratama');
        $response->assertSee('11.2');
        $response->assertSee('82.0');
        $response->assertSee('Pertahankan asupan gizi seimbang');
        $response->assertSee('Riwayat Pemeriksaan Pasien (2 Data)');
    }

    public function test_patient_card_redirects_from_measurement_id(): void
    {
        $user = User::factory()->create(['role' => 'petugas']);

        $subject = Subject::create([
            'user_id' => $user->id,
            'name' => 'Siti Aisyah',
            'normalized_name' => 'SITI AISYAH',
            'date_of_birth' => '2022-05-10',
            'gender' => 'P',
        ]);

        $measurement = Measurement::create([
            'subject_id' => $subject->id,
            'user_id' => $user->id,
            'measurement_date' => '2024-05-10',
            'category' => 'balita',
            'age_in_months' => 24,
            'age_in_years' => 2,
            'weight' => 12.0,
            'height' => 88.0,
        ]);

        $response = $this->get('/card/measurement/' . $measurement->id);

        $response->assertRedirect('/card/patient/' . $subject->id);
    }

    public function test_non_existent_patient_card_returns_404(): void
    {
        $response = $this->get('/card/patient/99999');

        $response->assertStatus(404);
    }
}
