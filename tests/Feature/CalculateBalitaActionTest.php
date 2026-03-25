<?php

namespace Tests\Feature;

use App\Actions\Measurement\CalculateBalitaAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CalculateBalitaActionTest extends TestCase
{
    use RefreshDatabase;

    private CalculateBalitaAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CalculateBalitaAction();

        // Seed necessary reference data for the test
        // Let's say age = 24 months, gender L, height = 85 cm
        
        DB::table('reference_balita_bbu')->insert([
            'gender' => 'L',
            'age_months' => 24,
            'neg3sd' => 9.0,
            'neg2sd' => 10.0,
            'neg1sd' => 11.0,
            'median' => 12.0,
            'pos1sd' => 13.0,
            'pos2sd' => 14.0,
            'pos3sd' => 15.0,
        ]);

        DB::table('reference_balita_tbu')->insert([
            'gender' => 'L',
            'age_months' => 24,
            'neg3sd' => 80.0,
            'neg2sd' => 82.0,
            'neg1sd' => 85.0,
            'median' => 87.0,
            'pos1sd' => 90.0,
            'pos2sd' => 92.0,
            'pos3sd' => 95.0,
        ]);

        DB::table('reference_balita_bbtb')->insert([
            'gender' => 'L',
            'height' => 85.0,
            'neg3sd' => 9.5,
            'neg2sd' => 10.5,
            'neg1sd' => 11.5,
            'median' => 12.5,
            'pos1sd' => 13.5,
            'pos2sd' => 14.5,
            'pos3sd' => 15.5,
        ]);
    }

    public function test_bbu_classification()
    {
        // weight = 12 (median => Gizi Baik)
        $result = $this->action->execute('L', 24, 12.0, 85.0, 'Berdiri');
        $this->assertEquals('Gizi Baik', $result['status_bbu']);

        // weight = 8.5 (< -3 SD => Gizi Buruk)
        $result = $this->action->execute('L', 24, 8.5, 85.0, 'Berdiri');
        $this->assertEquals('Gizi Buruk', $result['status_bbu']);

        // weight = 9.5 (-3 SD to -2 SD => Gizi Kurang)
        $result = $this->action->execute('L', 24, 9.5, 85.0, 'Berdiri');
        $this->assertEquals('Gizi Kurang', $result['status_bbu']);

        // weight = 14.5 (> +1 SD => Berisiko Gizi Lebih)
        $result = $this->action->execute('L', 24, 14.5, 85.0, 'Berdiri');
        $this->assertEquals('Berisiko Gizi Lebih', $result['status_bbu']);
    }

    public function test_tbu_classification()
    {
        // height = 87 (median => Normal)
        $result = $this->action->execute('L', 24, 12.0, 87.0, 'Berdiri');
        $this->assertEquals('Normal', $result['status_tbu']);

        // height = 79 (< -3 SD => Sangat Pendek)
        $result = $this->action->execute('L', 24, 12.0, 79.0, 'Berdiri');
        $this->assertEquals('Sangat Pendek', $result['status_tbu']);

        // height = 81 (-3 SD to -2 SD => Pendek)
        $result = $this->action->execute('L', 24, 12.0, 81.0, 'Berdiri');
        $this->assertEquals('Pendek', $result['status_tbu']);

        // height = 97 (> +3 SD => Tinggi)
        $result = $this->action->execute('L', 24, 12.0, 97.0, 'Berdiri');
        $this->assertEquals('Tinggi', $result['status_tbu']);
    }

    public function test_bbtb_classification()
    {
        // height = 85
        // weight = 12.5 (median => Gizi Baik)
        $result = $this->action->execute('L', 24, 12.5, 85.0, 'Berdiri');
        $this->assertEquals('Gizi Baik', $result['status_bbtb']);

        // weight = 9.0 (< -3 SD => Gizi Buruk)
        $result = $this->action->execute('L', 24, 9.0, 85.0, 'Berdiri');
        $this->assertEquals('Gizi Buruk', $result['status_bbtb']);

        // weight = 10.0 (-3 to -2 SD => Gizi Kurang)
        $result = $this->action->execute('L', 24, 10.0, 85.0, 'Berdiri');
        $this->assertEquals('Gizi Kurang', $result['status_bbtb']);

        // weight = 14.0 (+1 to +2 SD => Berisiko Gizi Lebih)
        $result = $this->action->execute('L', 24, 14.0, 85.0, 'Berdiri');
        $this->assertEquals('Berisiko Gizi Lebih', $result['status_bbtb']);

        // weight = 15.0 (+2 to +3 SD => Gizi Lebih)
        $result = $this->action->execute('L', 24, 15.0, 85.0, 'Berdiri');
        $this->assertEquals('Gizi Lebih', $result['status_bbtb']);

        // weight = 16.0 (> +3 SD => Obesitas)
        $result = $this->action->execute('L', 24, 16.0, 85.0, 'Berdiri');
        $this->assertEquals('Obesitas', $result['status_bbtb']);
    }

    public function test_height_adjustment_for_age_and_measurement_type()
    {
        DB::table('reference_balita_bbtb')->insert([
            'gender' => 'L',
            'height' => 85.5, // 85 rounded up will be 85.5 when adjusted
            'neg3sd' => 15.0,
            'neg2sd' => 16.0,
            'neg1sd' => 17.0,
            'median' => 18.0,
            'pos1sd' => 19.0,
            'pos2sd' => 20.0,
            'pos3sd' => 21.0,
        ]);

        // Age 23 months (<24), measuring standing
        // It will add 0.7 to height, so 85.0 becomes 85.7
        // Then CalculateBalitaAction rounds 85.7 to nearest 0.5 => 85.5
        // At 85.5 reference, weight = 12.0 is < 15.0 (-3 SD), so it should be 'Gizi Buruk'
        // If it didn't adjust height, it would use 85.0 reference (where median is 12.5), 12.0 would be 'Gizi Baik'
        $resultStanding = $this->action->execute('L', 23, 12.0, 85.0, 'berdiri');
        $this->assertEquals('Gizi Buruk', $resultStanding['status_bbtb']);

        // Age 25 months (>=24), measuring supine (berbaring)
        // It will subtract 0.7 from height.
        // Let's use height 86.2, which becomes 86.2 - 0.7 = 85.5
        // Nearest 0.5 is 85.5
        // weight = 18.0 is median for 85.5 => 'Gizi Baik'
        $resultSupine = $this->action->execute('L', 25, 18.0, 86.2, 'berbaring');
        $this->assertEquals('Gizi Baik', $resultSupine['status_bbtb']);
    }
}
