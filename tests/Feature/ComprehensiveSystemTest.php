<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ComprehensiveSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Auth Flow: Registration, Login, Token Usage.
     */
    public function test_auth_flow_works_correctly(): void
    {
        // 1. Register
        $response = $this->postJson('/api/auth/register', [
            'name' => 'New Petugas',
            'email' => 'petugas@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(201);

        // 2. Login
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'petugas@test.com',
            'password' => 'password',
        ]);
        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['token']]);

        $token = $loginResponse->json('data.token');

        // 3. Access Protected Route
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');
        $meResponse->assertStatus(200)
            ->assertJsonPath('data.email', 'petugas@test.com');
    }

    /**
     * Test Role-Based Access Control (RBAC) & Case-Insensitivity.
     */
    public function test_admin_role_access_and_case_insensitivity(): void
    {
        // Petugas User
        $petugas = User::factory()->create(['role' => 'petugas']);
        Sanctum::actingAs($petugas);

        // Petugas accessing Admin route -> 403
        $this->getJson('/api/admin/users')
            ->assertStatus(403);

        // Admin User (Lowercase)
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        // Admin accessing Admin route -> 200
        $this->getJson('/api/admin/users')
            ->assertStatus(200);

        // Admin User (Uppercase - Case Insensitivity Check via Model)
        // Note: Database has Check Constraint for lowercase, so we test the Model logic in-memory
        $adminInMemory = new User(['role' => 'ADMIN']);
        $this->assertTrue($adminInMemory->isAdmin());

        // Admin User (Mixed Case)
        $adminInMemory->role = 'Admin';
        $this->assertTrue($adminInMemory->isAdmin());
    }

    /**
     * Test Measurement Logic: Central Obesity Status.
     */
    public function test_measurement_calculation_central_obesity(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create an ADULT subject (30 years old) to ensure 'dewasa' category
        $subject = Subject::factory()->create([
            'user_id' => $user->id,
            'date_of_birth' => now()->subYears(30)->format('Y-m-d'),
            'gender' => 'L', // Male
        ]);

        // Case 1: Normal Male (Waist 85 <= 90)
        $responseNormal = $this->postJson("/api/subjects/{$subject->id}/measurements", [
            'height' => 170,
            'weight' => 70,
            'waist_circumference' => 85,
            'arm_circumference' => 30, // Optional
            'head_circumference' => 50, // Optional
            'measurement_date' => now()->format('Y-m-d'),
        ]);

        $responseNormal->assertStatus(201)
            ->assertJsonPath('data.measurement.result.central_obesity_status', 'Normal');

        // Case 2: Obese Male (Waist 95 > 90)
        $responseObese = $this->postJson("/api/subjects/{$subject->id}/measurements", [
            'height' => 170,
            'weight' => 70,
            'waist_circumference' => 95,
            'measurement_date' => now()->format('Y-m-d'),
        ]);

        $responseObese->assertStatus(201)
            ->assertJsonPath('data.measurement.result.central_obesity_status', 'Obesitas Sentral');
    }

    /**
     * Test Endpoint Health & Debug.
     */
    public function test_health_and_debug_endpoints(): void
    {
        $this->getJson('/api/health')
            ->assertStatus(200)
            ->assertJsonPath('status', 'ok');


    }
}
