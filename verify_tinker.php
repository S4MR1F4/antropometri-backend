<?php
// Test cases
$cases = [
    [
        'name' => 'Adult',
        'gender' => 'L',
        'ageMonths' => 432,
        'weight' => 85.5,
        'height' => 175.0,
        'waist' => 105.0
    ],
    [
        'name' => 'Remaja',
        'gender' => 'P',
        'ageMonths' => 181,
        'weight' => 55.0,
        'height' => 160.0
    ],
    [
        'name' => 'Balita',
        'gender' => 'L',
        'ageMonths' => 24,
        'weight' => 12.0,
        'height' => 85.0,
        'type' => 'berdiri'
    ]
];

foreach ($cases as $c) {
    echo "--- Testing {$c['name']} ---\n";
    $start = microtime(true);

    $results = [];
    if ($c['name'] === 'Adult') {
        $action = new \App\Actions\Measurement\CalculateDewasaAction();
        $results = $action->execute($c['gender'], $c['weight'], $c['height'], $c['waist']);
    } elseif ($c['name'] === 'Remaja') {
        $action = new \App\Actions\Measurement\CalculateRemajaAction();
        $results = $action->execute($c['gender'], $c['ageMonths'], $c['weight'], $c['height']);
    } else {
        $action = new \App\Actions\Measurement\CalculateBalitaAction();
        $results = $action->execute($c['gender'], $c['ageMonths'], $c['weight'], $c['height'], $c['type']);
    }

    $end = microtime(true);
    echo "Time: " . ($end - $start) . "s\n";
    echo "Data: W=" . $c['weight'] . " H=" . $c['height'] . " Category=" . strtolower($c['name']) . "\n";
    echo "Result Keys: " . implode(', ', array_keys($results)) . "\n";
    echo "Result Summary: " . json_encode($results) . "\n\n";
}
