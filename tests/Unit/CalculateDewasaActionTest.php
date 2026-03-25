<?php

namespace Tests\Unit;

use App\Actions\Measurement\CalculateDewasaAction;
use PHPUnit\Framework\TestCase;

class CalculateDewasaActionTest extends TestCase
{
    private CalculateDewasaAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CalculateDewasaAction();
    }

    public function test_bmi_classification_boundaries()
    {
        // Sangat Kurus: BMI < 17
        $result1 = $this->action->execute('L', 45, 165); // 45 / (1.65*1.65) = 16.52
        $this->assertEquals('Sangat Kurus', $result1['status_bmi']);

        // Kurus: 17 - 18.5
        $result2 = $this->action->execute('L', 49, 165); // 18.0
        $this->assertEquals('Kurus', $result2['status_bmi']);

        // Normal: 18.5 - 25.0
        $result3 = $this->action->execute('L', 60, 165); // 22.0
        $this->assertEquals('Normal', $result3['status_bmi']);

        // Gemuk: 25.1 - 27.0
        $result4 = $this->action->execute('L', 70, 165); // 25.7
        $this->assertEquals('Gemuk', $result4['status_bmi']);

        // Obesitas: > 27.0
        $result5 = $this->action->execute('L', 80, 165); // 29.38
        $this->assertEquals('Obesitas', $result5['status_bmi']);
    }

    public function test_central_obesity_for_male()
    {
        // Threshold male: 90
        $resultNormal = $this->action->execute('L', 60, 165, 85);
        $this->assertFalse($resultNormal['has_central_obesity']);
        $this->assertEquals('Normal', $resultNormal['status_central_obesity']);

        $resultObese = $this->action->execute('L', 60, 165, 95);
        $this->assertTrue($resultObese['has_central_obesity']);
        $this->assertEquals('Obesitas Sentral', $resultObese['status_central_obesity']);
    }

    public function test_central_obesity_for_female()
    {
        // Threshold female: 80
        $resultNormal = $this->action->execute('P', 60, 165, 75);
        $this->assertFalse($resultNormal['has_central_obesity']);
        $this->assertEquals('Normal', $resultNormal['status_central_obesity']);

        $resultObese = $this->action->execute('P', 60, 165, 85);
        $this->assertTrue($resultObese['has_central_obesity']);
        $this->assertEquals('Obesitas Sentral', $resultObese['status_central_obesity']);
    }

    public function test_lila_for_pregnant_women()
    {
        // Pregnant, KEK (< 23.5)
        $resultKEK = $this->action->execute('P', 60, 165, null, 22.0, true);
        $this->assertEquals('Risiko KEK', $resultKEK['status_kek']);
        $this->assertEquals('Ibu Hamil (Normal)', $resultKEK['status_bmi']); // BMI is Normal, but wrapped
        
        // Pregnant, Normal (>= 23.5)
        $resultNormal = $this->action->execute('P', 60, 165, null, 24.0, true);
        $this->assertEquals('Normal', $resultNormal['status_kek']);
    }

    public function test_waist_circumference_is_ignored_when_pregnant()
    {
        // Pregnant woman with waist circumference 90 (usually obesity)
        // Central obesity shouldn't be calculated
        $result = $this->action->execute('P', 60, 165, 90, null, true);
        $this->assertEquals('Normal (Hamil)', $result['status_central_obesity']);
        $this->assertArrayNotHasKey('has_central_obesity', $result);
    }
}
