<?php
require __DIR__.'/vendor/autoload.php';
$inputFileName = 'd:/SAMRIFA/projects/antropometri/docs/Template_Perhitungan_Antropometri.xlsx';
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
echo "Sheets:\n";
foreach ($spreadsheet->getSheetNames() as $name) {
    echo "- $name\n";
    $worksheet = $spreadsheet->getSheetByName($name);
    $rows = $worksheet->toArray();
    echo "First 10 rows of $name:\n";
    for($i=0; $i<min(10, count($rows)); $i++) {
        echo implode(" | ", array_map(function($v) { return substr((string)$v, 0, 20); }, $rows[$i])) . "\n";
    }
    echo "\n";
}
