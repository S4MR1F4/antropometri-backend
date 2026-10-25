<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Antropometri');
        $response->assertSee('Klien / Pasien Terdaftar');
        $response->assertSee('Total Pemeriksaan Antropometri');
    }

    public function test_public_coverage_stats_api_returns_anonymized_metrics(): void
    {
        $user = User::factory()->create(['role' => 'petugas']);
        $subject = Subject::factory()->create(['user_id' => $user->id]);
        Measurement::factory()->count(2)->create([
            'subject_id' => $subject->id,
            'user_id' => $user->id,
            'category' => 'balita',
            'status_bbtb' => 'Gizi Baik',
        ]);

        $response = $this->getJson('/api/public/coverage-stats');

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => [
                'total_clients',
                'total_examinations',
                'total_staff',
                'avg_per_client',
                'normal_percentage',
                'categories' => [
                    'balita',
                    'remaja',
                    'dewasa',
                    'bumil',
                ],
                'traffic' => [
                    'total_activities',
                    'total_sync_batches',
                    'total_records_synced',
                    'recent_30d',
                    'avg_latency',
                    'uptime',
                ],
                'last_updated',
            ],
        ]);

        // Zero PII verification: verify no client names or private health data leaked
        $json = $response->json();
        $this->assertEquals(1, $json['data']['total_clients']);
        $this->assertEquals(2, $json['data']['total_examinations']);
        $this->assertEquals(2.0, $json['data']['avg_per_client']);
        $this->assertArrayHasKey('traffic', $json['data']);
    }

    public function test_dashboard_route_redirects_permanently_to_root(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/');
        $response->assertStatus(301);
    }

    public function test_landing_page_does_not_contain_personal_patient_names(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertDontSee('Ananda Pratama');
        $response->assertSee('ID Subjek (Anonim)');
        $response->assertSee('Perlindungan Privasi Mutlak');
        $response->assertSee('Telemetri & Trafik Operasional Lapangan');
    }

    public function test_security_hardening_blocks_php2shell_and_cve_2024_4577_attacks(): void
    {
        // Malicious request with PHP argument injection / php2shell parameters
        $response1 = $this->get('/?auto_prepend_file=php://input');
        $response1->assertForbidden();

        $response2 = $this->get('/?allow_url_include=1');
        $response2->assertForbidden();

        $response3 = $this->get('/?wrapper=phar://malicious.phar');
        $response3->assertForbidden();
    }
}
