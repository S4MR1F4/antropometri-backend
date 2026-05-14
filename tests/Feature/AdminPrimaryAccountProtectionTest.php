<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPrimaryAccountProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_other_admin_cannot_update_primary_account(): void
    {
        $primary = User::factory()->admin()->create([
            'email' => 'antropometri@samrifa.com',
        ]);
        $otherAdmin = User::factory()->admin()->create();

        $response = $this->actingAs($otherAdmin)
            ->putJson("/api/admin/users/{$primary->id}", [
                'name' => 'Changed Owner',
                'email' => 'antropometri@samrifa.com',
                'role' => 'admin',
            ]);

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Akun utama hanya dapat diubah oleh pemilik akun utama.');
    }

    public function test_user_listing_marks_current_and_primary_accounts(): void
    {
        $primary = User::factory()->admin()->create([
            'email' => 'antropometri@samrifa.com',
        ]);
        $otherAdmin = User::factory()->admin()->create();

        $response = $this->actingAs($otherAdmin)
            ->getJson('/api/admin/users?per_page=100');

        $response->assertStatus(200);

        $users = collect($response->json('data.users'));
        $primaryPayload = $users->firstWhere('id', $primary->id);
        $currentPayload = $users->firstWhere('id', $otherAdmin->id);

        $this->assertTrue($primaryPayload['is_primary']);
        $this->assertFalse($primaryPayload['can_manage']);
        $this->assertTrue($currentPayload['is_current_user']);
    }
}
