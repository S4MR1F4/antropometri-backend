<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Subject;
use App\Models\Measurement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure reference data exists for observers/calculations
        \Illuminate\Support\Facades\DB::table('reference_balita_bbu')->insert([
            ['gender' => 'L', 'age_months' => 12, 'neg3sd' => 7.8, 'neg2sd' => 8.6, 'neg1sd' => 9.4, 'median' => 10.4, 'pos1sd' => 11.5, 'pos2sd' => 12.7, 'pos3sd' => 14.1],
            ['gender' => 'P', 'age_months' => 12, 'neg3sd' => 7.1, 'neg2sd' => 7.9, 'neg1sd' => 8.7, 'median' => 9.6, 'pos1sd' => 10.7, 'pos2sd' => 11.9, 'pos3sd' => 13.3],
        ]);

        \Illuminate\Support\Facades\DB::table('reference_balita_tbu')->insert([
            ['gender' => 'L', 'age_months' => 12, 'neg3sd' => 68.6, 'neg2sd' => 71.0, 'neg1sd' => 73.4, 'median' => 75.7, 'pos1sd' => 78.1, 'pos2sd' => 80.5, 'pos3sd' => 82.9],
        ]);

        \Illuminate\Support\Facades\DB::table('reference_balita_bbtb')->insert([
            ['gender' => 'L', 'height' => 75.0, 'neg3sd' => 8.0, 'neg2sd' => 8.6, 'neg1sd' => 9.3, 'median' => 10.2, 'pos1sd' => 11.1, 'pos2sd' => 12.2, 'pos3sd' => 13.3],
        ]);
    }

    public function test_admin_can_import_measurements_from_excel()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Mock the Excel import to avoid needing a real file during initial testing,
        // but it's better to test the real logic. 
        // Let's create a real temporary excel file for a true integration test.
        
        // Or we can just test the Import class directly in a Unit-like Feature test
        // by passing a collection to it.
        
        $file = UploadedFile::fake()->create('measurements.xlsx');

        // We need to use Maatwebsite\Excel persistent mocks if we want to mock the behavior
        // But here we want to test our controller and import logic.
        
        // Since generating a real Excel in a test is complex without a library,
        // we'll mock the Excel facade to verify it's called with the right class.
        Excel::fake();

        $response = $this->actingAs($admin)
            ->postJson('/api/import/measurements', [
                'file' => $file
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'imported_count',
                    'skipped_count'
                ]
            ]);

        Excel::assertImported('measurements.xlsx', function (\App\Imports\MeasurementsImport $import) {
            return true;
        });
    }

    public function test_petugas_can_also_import_measurements()
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $file = UploadedFile::fake()->create('measurements.xlsx');

        Excel::fake();

        $response = $this->actingAs($petugas)
            ->postJson('/api/import/measurements', [
                'file' => $file
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'imported_count',
                    'skipped_count'
                ]
            ]);

        Excel::assertImported('measurements.xlsx', function (\App\Imports\MeasurementsImport $import) {
            return true;
        });
    }
}
