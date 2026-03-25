<?php

namespace Tests\Unit;

use App\Actions\Measurement\CalculateRemajaAction;
use App\Models\ReferenceRemajaImtu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Illuminate\Support\Facades\DB;

class CalculateRemajaActionTest extends TestCase
{
    use RefreshDatabase;

    private CalculateRemajaAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CalculateRemajaAction();

        // Seed necessary reference data for the test
        // Let's say age = 120 months (10 years)
        DB::table('reference_remaja_imtu')->insert([
            'gender' => 'L',
            'age_months' => 120,
            'neg3sd' => 12.0,
            'neg2sd' => 13.0,
            'neg1sd' => 14.0,
            'median' => 15.0,
            'pos1sd' => 16.0,
            'pos2sd' => 17.0,
            'pos3sd' => 18.0,
        ]);
    }

    public function test_imtu_classification()
    {
        // BMI = weight / height^2
        // height = 140cm (1.4m), weight = 30kg
        // BMI = 30 / (1.4*1.4) = 15.3 (Around median, Normal / Gizi Baik)
        
        $resultBaik = $this->action->execute('L', 120, 30, 140);
        $this->assertEquals('Gizi Baik', $resultBaik['status_imtu']);

        // weight = 24kg
        // BMI = 24 / 1.96 = 12.24 (Between -3 and -2 SD -> Gizi Kurang)
        $resultKurang = $this->action->execute('L', 120, 24, 140);
        $this->assertEquals('Gizi Kurang', $resultKurang['status_imtu']);

        // weight = 20kg
        // BMI = 20 / 1.96 = 10.2 (Below -3 SD -> Gizi Buruk)
        $resultBuruk = $this->action->execute('L', 120, 20, 140);
        $this->assertEquals('Gizi Buruk', $resultBuruk['status_imtu']);
        
        // weight = 33kg
        // BMI = 33 / 1.96 = 16.8 (Between +1 and +2 SD -> Berisiko Gizi Lebih)
        $resultRisiko = $this->action->execute('L', 120, 33, 140);
        $this->assertEquals('Berisiko Gizi Lebih', $resultRisiko['status_imtu']);

        // weight = 34kg
        // BMI = 34 / 1.96 = 17.3 (Between +2 and +3 SD -> Gizi Lebih)
        $resultLebih = $this->action->execute('L', 120, 34, 140);
        $this->assertEquals('Gizi Lebih', $resultLebih['status_imtu']);

        // weight = 40kg
        // BMI = 40 / 1.96 = 20.4 (> +3 SD -> Obesitas)
        $resultObesitas = $this->action->execute('L', 120, 40, 140);
        $this->assertEquals('Obesitas', $resultObesitas['status_imtu']);
    }

}
