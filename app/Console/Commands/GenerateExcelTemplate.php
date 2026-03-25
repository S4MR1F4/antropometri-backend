<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class GenerateExcelTemplate extends Command
{
    protected $signature = 'app:generate-excel-template';
    protected $description = 'Generate Template Perhitungan Antropometri lengkap dengan Chart dan Validasi.';

    public function handle()
    {
        $this->info('Memulai pembuatan Template Excel Canggih...');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        // --- Hidden DB Reference Sheets ---
        $this->createReferenceSheet($spreadsheet, 'Ref_BBU', 'reference_balita_bbu', 'age_months');
        $this->createReferenceSheet($spreadsheet, 'Ref_TBU', 'reference_balita_tbu', 'age_months');
        $this->createReferenceSheet($spreadsheet, 'Ref_BBTB', 'reference_balita_bbtb', 'height');
        $this->createReferenceSheet($spreadsheet, 'Ref_IMTU', 'reference_remaja_imtu', 'age_months');

        // --- Template Input Sheet ---
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Template Input');
        $spreadsheet->setActiveSheetIndexByName('Template Input');

        $headers = [
            'No',
            'NIK',
            'Nama Pasien',
            'Tanggal Lahir',
            'Tanggal Periksa',
            'Jenis Kelamin (L/P)',
            'Hamil? (Y/T)',
            'Berat Badan (kg)',
            'Tinggi Badan (cm)',
            'LILA (cm)',
            'Lingkar Kepala (cm)',
            'Umur (Bulan) [Auto]',
            'Umur (Tahun) [Auto]',
            'Kategori [Auto]',
            'Z-Score BB/U [Auto]',
            'Status BB/U [Auto]',
            'Z-Score TB/U [Auto]',
            'Status TB/U [Auto]',
            'Z-Score BB/TB [Auto]',
            'Status BB/TB [Auto]',
            'Z-Score IMT/U (Remaja) [Auto]',
            'Status IMT/U (Remaja) [Auto]',
            'IMT Dewasa [Auto]',
            'Status IMT Dewasa [Auto]',
            'Status LILA (Bumil) [Auto]',
            'Rekomendasi [Auto]' // Kolom Y/Z
        ];

        // Header Style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        $sheet->fromArray([$headers], NULL, 'A1');
        $sheet->getStyle('A1:Z1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(55);
        $sheet->freezePane('A2');

        $cols = [
            'A' => 5,
            'B' => 20,
            'C' => 25,
            'D' => 16,
            'E' => 16,
            'F' => 14,
            'G' => 12,
            'H' => 14,
            'I' => 14,
            'J' => 12,
            'K' => 15,
            'L' => 14,
            'M' => 14,
            'N' => 14,
            'O' => 14,
            'P' => 20,
            'Q' => 14,
            'R' => 22,
            'S' => 14,
            'T' => 20,
            'U' => 16,
            'V' => 22,
            'W' => 14,
            'X' => 20,
            'Y' => 18,
            'Z' => 45
        ];
        foreach ($cols as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $maxRows = 200;

        $inputStyle = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $autoStyle = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $sheet->getStyle("A2:K$maxRows")->applyFromArray($inputStyle);
        $sheet->getStyle("L2:Z$maxRows")->applyFromArray($autoStyle);

        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle("A2:K$maxRows")->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

        // --- Data Validation & Dropdowns ---

        // Validation for Date (Memaksa Date Format dan trigger Date Picker native di beberapa platform Excel)
        $dvDate = $sheet->getCell('D2')->getDataValidation();
        $dvDate->setType(DataValidation::TYPE_DATE);
        $dvDate->setErrorStyle(DataValidation::STYLE_STOP);
        $dvDate->setAllowBlank(true);
        $dvDate->setShowInputMessage(true);
        $dvDate->setPromptTitle('Pilih/Ketik Tanggal');
        $dvDate->setPrompt('Gunakan format: YYYY-MM-DD. (Contoh: 2024-05-20)');

        $dvGender = $sheet->getCell('F2')->getDataValidation();
        $dvGender->setType(DataValidation::TYPE_LIST);
        $dvGender->setAllowBlank(true);
        $dvGender->setShowDropDown(true);
        $dvGender->setFormula1('"L,P"');

        $dvHamil = $sheet->getCell('G2')->getDataValidation();
        $dvHamil->setType(DataValidation::TYPE_LIST);
        $dvHamil->setAllowBlank(true);
        $dvHamil->setShowDropDown(true);
        $dvHamil->setFormula1('"Y,T"');

        for ($r = 2; $r <= $maxRows; $r++) {
            $sheet->getCell("D{$r}")->setDataValidation(clone $dvDate);
            $sheet->getCell("E{$r}")->setDataValidation(clone $dvDate);
            $sheet->getCell("F{$r}")->setDataValidation(clone $dvGender);
            $sheet->getCell("G{$r}")->setDataValidation(clone $dvHamil);

            // Format Numbering as proper Date
            $sheet->getStyle("D{$r}")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            $sheet->getStyle("E{$r}")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        }

        // --- SUMIFS Based Master Formulas ---
        for ($r = 2; $r <= $maxRows; $r++) {
            $sheet->setCellValue("L{$r}", "=IF(AND(D{$r}<>\"\", E{$r}<>\"\"), DATEDIF(D{$r}, E{$r}, \"m\"), \"\")");
            $sheet->setCellValue("M{$r}", "=IF(L{$r}<>\"\", INT(L{$r}/12), \"\")");
            $sheet->setCellValue("N{$r}", "=IF(G{$r}=\"Y\", \"Ibu Hamil\", IF(L{$r}<>\"\", IF(L{$r}<=60, \"Balita\", IF(L{$r}<=216, \"Remaja\", \"Dewasa\")), \"\"))");

            // Variables for calculations
            $bb = "H$r";
            $tb = "I$r";

            // BBU
            $c_bbu = "COUNTIFS(Ref_BBU!B:B, F$r, Ref_BBU!A:A, L$r)";
            $m_bbu = "SUMIFS(Ref_BBU!C:C, Ref_BBU!B:B, F$r, Ref_BBU!A:A, L$r)";
            $n_bbu = "SUMIFS(Ref_BBU!D:D, Ref_BBU!B:B, F$r, Ref_BBU!A:A, L$r)";
            $p_bbu = "SUMIFS(Ref_BBU!E:E, Ref_BBU!B:B, F$r, Ref_BBU!A:A, L$r)";
            $sheet->setCellValue("O{$r}", "=IF(AND(N{$r}=\"Balita\", $bb<>\"\"), IF($c_bbu>0, ROUND(($bb - $m_bbu) / IF($bb >= $m_bbu, $p_bbu - $m_bbu, $m_bbu - $n_bbu), 2), \"N/A\"), \"-\")");
            $sheet->setCellValue("P{$r}", "=IF(O{$r}=\"-\", \"-\", IF(O{$r}=\"N/A\", \"N/A\", IF(O{$r}<-3, \"Gizi Buruk\", IF(O{$r}<-2, \"Gizi Kurang\", IF(O{$r}<=1, \"Gizi Baik\", \"Beresiko Lebih\")))))");

            // TBU
            $c_tbu = "COUNTIFS(Ref_TBU!B:B, F$r, Ref_TBU!A:A, L$r)";
            $m_tbu = "SUMIFS(Ref_TBU!C:C, Ref_TBU!B:B, F$r, Ref_TBU!A:A, L$r)";
            $n_tbu = "SUMIFS(Ref_TBU!D:D, Ref_TBU!B:B, F$r, Ref_TBU!A:A, L$r)";
            $p_tbu = "SUMIFS(Ref_TBU!E:E, Ref_TBU!B:B, F$r, Ref_TBU!A:A, L$r)";
            $sheet->setCellValue("Q{$r}", "=IF(AND(N{$r}=\"Balita\", $tb<>\"\"), IF($c_tbu>0, ROUND(($tb - $m_tbu) / IF($tb >= $m_tbu, $p_tbu - $m_tbu, $m_tbu - $n_tbu), 2), \"N/A\"), \"-\")");
            $sheet->setCellValue("R{$r}", "=IF(Q{$r}=\"-\", \"-\", IF(Q{$r}=\"N/A\", \"N/A\", IF(Q{$r}<-3, \"Sangat Pendek\", IF(Q{$r}<-2, \"Pendek\", IF(Q{$r}<=3, \"Normal\", \"Tinggi\")))))");

            // BBTB
            $h_rnd = "ROUND($tb*2,0)/2";
            $c_bbtb = "COUNTIFS(Ref_BBTB!B:B, F$r, Ref_BBTB!A:A, $h_rnd)";
            $m_bbtb = "SUMIFS(Ref_BBTB!C:C, Ref_BBTB!B:B, F$r, Ref_BBTB!A:A, $h_rnd)";
            $n_bbtb = "SUMIFS(Ref_BBTB!D:D, Ref_BBTB!B:B, F$r, Ref_BBTB!A:A, $h_rnd)";
            $p_bbtb = "SUMIFS(Ref_BBTB!E:E, Ref_BBTB!B:B, F$r, Ref_BBTB!A:A, $h_rnd)";
            $sheet->setCellValue("S{$r}", "=IF(AND(N{$r}=\"Balita\", $bb<>\"\", $tb<>\"\"), IF($c_bbtb>0, ROUND(($bb - $m_bbtb) / IF($bb >= $m_bbtb, $p_bbtb - $m_bbtb, $m_bbtb - $n_bbtb), 2), \"N/A\"), \"-\")");
            $sheet->setCellValue("T{$r}", "=IF(S{$r}=\"-\", \"-\", IF(S{$r}=\"N/A\", \"N/A\", IF(S{$r}<-3, \"Gizi Buruk\", IF(S{$r}<-2, \"Gizi Kurang\", IF(S{$r}<=1, \"Gizi Baik\", IF(S{$r}<=2, \"Beresiko Lebih\", IF(S{$r}<=3, \"Gizi Lebih\", \"Obesitas\")))))))");

            // IMTU
            $imtRaw = "($bb / (($tb/100)*($tb/100)))";
            $c_imtu = "COUNTIFS(Ref_IMTU!B:B, F$r, Ref_IMTU!A:A, L$r)";
            $m_imtu = "SUMIFS(Ref_IMTU!C:C, Ref_IMTU!B:B, F$r, Ref_IMTU!A:A, L$r)";
            $n_imtu = "SUMIFS(Ref_IMTU!D:D, Ref_IMTU!B:B, F$r, Ref_IMTU!A:A, L$r)";
            $p_imtu = "SUMIFS(Ref_IMTU!E:E, Ref_IMTU!B:B, F$r, Ref_IMTU!A:A, L$r)";
            $sheet->setCellValue("U{$r}", "=IF(AND(N{$r}=\"Remaja\", $bb<>\"\", $tb<>\"\"), IF($c_imtu>0, ROUND(($imtRaw - $m_imtu) / IF($imtRaw >= $m_imtu, $p_imtu - $m_imtu, $m_imtu - $n_imtu), 2), \"N/A\"), \"-\")");
            $sheet->setCellValue("V{$r}", "=IF(U{$r}=\"-\", \"-\", IF(U{$r}=\"N/A\", \"N/A\", IF(U{$r}<-3, \"Gizi Buruk\", IF(U{$r}<-2, \"Gizi Kurang\", IF(U{$r}<=1, \"Gizi Baik\", IF(U{$r}<=2, \"Gizi Lebih\", \"Obesitas\"))))))");

            // Dewasa & Ibu Hamil
            $sheet->setCellValue("W{$r}", "=IF(AND(N{$r}=\"Dewasa\", $bb<>\"\", $tb<>\"\"), ROUND($imtRaw, 2), \"-\")");
            $sheet->setCellValue("X{$r}", "=IF(W{$r}=\"-\", \"-\", IF(W{$r}<18.5, \"Kurus\", IF(W{$r}<=25, \"Normal\", IF(W{$r}<=27, \"Gemuk (Overweight)\", \"Obesitas\"))))");
            $sheet->setCellValue("Y{$r}", "=IF(N{$r}=\"Ibu Hamil\", IF(J{$r}=\"\", \"N/A\", IF(J{$r}<23.5, \"KEK\", \"Normal\")), \"-\")");

            // Rekomendasi Pintar Bersarang
            $rec = "=IF(N{$r}=\"Ibu Hamil\", IF(Y{$r}=\"KEK\", \"Risiko KEK! Tingkatkan gizi, rujuk ke faskes/konsultasi bidan.\", \"Normal, pertahankan gizi & rutin periksa.\"), ";
            $rec .= "IF(N{$r}=\"Balita\", IF(OR(P{$r}=\"Gizi Buruk\", T{$r}=\"Gizi Buruk\"), \"Rujuk ke RS/Puskesmas segera untuk penanganan Gizi Buruk!\", IF(OR(R{$r}=\"Sangat Pendek\", R{$r}=\"Pendek\"), \"Indikasi Stunting! Konsultasi dokter anak, perbanyak protein.\", IF(OR(P{$r}=\"Gizi Kurang\", T{$r}=\"Gizi Kurang\"), \"Perlu intervensi gizi tambahan & pantau ketat bulanan.\", IF(OR(T{$r}=\"Obesitas\", T{$r}=\"Gizi Lebih\"), \"Awas kelebihan gizi, perhatikan pola makan padat energi.\", \"Sehat, pertahankan asupan gizi makro dan mikro.\")))), ";
            $rec .= "IF(N{$r}=\"Remaja\", IF(V{$r}=\"Gizi Buruk\", \"Segera rujuk dan perbaiki asupan gizi.\", IF(V{$r}=\"Gizi Kurang\", \"Tingkatkan asupan gizi dan kalori harian.\", IF(OR(V{$r}=\"Obesitas\", V{$r}=\"Gizi Lebih\"), \"Batasi kalori, tingkatkan aktivitas olahraga.\", \"Normal, terapkan hidup sehat & gizi seimbang.\"))), ";
            $rec .= "IF(N{$r}=\"Dewasa\", IF(X{$r}=\"Kurus\", \"Tingkatkan energi, protein dan massa otot.\", IF(OR(X{$r}=\"Obesitas\", X{$r}=\"Gemuk (Overweight)\"), \"Kurangi surplus kalori tinggi, jadwalkan defisit olahraga.\", \"Ideal, pertahankan gaya hidup sehat.\")), \"Lengkapi Data!\")";
            $rec .= ")))";
            $sheet->setCellValue("Z{$r}", $rec);
        }

        // --- Sheet Rekapitulasi & Chart ---
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Rekapitulasi');
        $spreadsheet->setActiveSheetIndexByName('Rekapitulasi');

        $summarySheet->setCellValue('A1', 'REKAPITULASI HASIL ANTROPOMETRI (AUTO-STATISTIC)');
        $summarySheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $summarySheet->mergeCells('A1:E1');

        $this->buildTable($summarySheet, 'Status Gizi (BB/U) - Balita', 'A4', [
            ['Gizi Buruk', 'Gizi Kurang', 'Gizi Baik', 'Beresiko Lebih'],
            ['P:P', '"Gizi Buruk"', '"Gizi Kurang"', '"Gizi Baik"', '"Beresiko Lebih"']
        ]);

        $this->buildTable($summarySheet, 'Status Stunting (TB/U) - Balita', 'E4', [
            ['Sangat Pendek (Stunting)', 'Pendek (Stunting)', 'Normal', 'Tinggi'],
            ['R:R', '"Sangat Pendek"', '"Pendek"', '"Normal"', '"Tinggi"']
        ]);

        $this->buildTable($summarySheet, 'Status Gizi (BB/TB) - Balita', 'I4', [
            ['Gizi Buruk', 'Gizi Kurang', 'Gizi Baik', 'Beresiko Lebih', 'Gizi Lebih', 'Obesitas'],
            ['T:T', '"Gizi Buruk"', '"Gizi Kurang"', '"Gizi Baik"', '"Beresiko Lebih"', '"Gizi Lebih"', '"Obesitas"']
        ]);

        $this->buildCategoryTable($summarySheet, 'A16');

        // Add Chart for Demografi Gender vs Stunting
        $this->addChartToSummary($summarySheet);

        // Save
        $dir = base_path('../docs');
        if (!is_dir($dir))
            mkdir($dir, 0755, true);
        $filePath = $dir . '/Template_Perhitungan_Antropometri.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->setPreCalculateFormulas(false);
        $writer->save($filePath);

        $this->info("Template Berhasil dibuat beserta Chart & Rekapitulasi di: {$filePath}");
    }

    private function buildTable($sheet, $title, $startCell, $data)
    {
        $col = $startCell[0];
        $row = (int) substr($startCell, 1);

        $sheet->setCellValue("{$col}{$row}", $title);
        $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("{$col}{$row}", "Status");
        $col1 = chr(ord($col) + 1);
        $col2 = chr(ord($col) + 2);

        $sheet->setCellValue("{$col1}{$row}", "Laki-laki");
        $sheet->setCellValue("{$col2}{$row}", "Perempuan");
        $sheet->getStyle("{$col}{$row}:{$col2}{$row}")->getFont()->setBold(true);
        $sheet->getStyle("{$col}{$row}:{$col2}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');

        $sheet->getColumnDimension($col)->setWidth(25);
        $sheet->getColumnDimension($col1)->setWidth(12);
        $sheet->getColumnDimension($col2)->setWidth(12);

        $row++;
        $labels = $data[0];
        $targetCol = $data[1][0]; // "P:P"
        for ($i = 0; $i < count($labels); $i++) {
            $sheet->setCellValue("{$col}{$row}", $labels[$i]);
            $sheet->setCellValue("{$col1}{$row}", "=COUNTIFS('Template Input'!$targetCol, {$data[1][$i + 1]}, 'Template Input'!F:F, \"L\")");
            $sheet->setCellValue("{$col2}{$row}", "=COUNTIFS('Template Input'!$targetCol, {$data[1][$i + 1]}, 'Template Input'!F:F, \"P\")");
            $row++;
        }
    }

    private function buildCategoryTable($sheet, $startCell)
    {
        $sheet->setCellValue($startCell, "Demografi Kategori");
        $sheet->getStyle($startCell)->getFont()->setBold(true);
        $row = (int) substr($startCell, 1) + 1;

        $categories = ['Balita', 'Remaja', 'Dewasa', 'Ibu Hamil'];
        foreach ($categories as $cat) {
            $sheet->setCellValue("A{$row}", $cat);
            $sheet->setCellValue("B{$row}", "=COUNTIF('Template Input'!N:N, \"{$cat}\")");
            $row++;
        }
    }

    private function addChartToSummary($sheet)
    {
        // Chart 1: Demografi Kategori (Pie)
        $dataSeriesLabels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Rekapitulasi!$B$16', null, 1)];
        $xAxisTickValues = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Rekapitulasi!$A$17:$A$20', null, 4)];
        $dataSeriesValues = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Rekapitulasi!$B$17:$B$20', null, 4)];

        $series = new DataSeries(DataSeries::TYPE_PIECHART, null, range(0, count($dataSeriesValues) - 1), $dataSeriesLabels, $xAxisTickValues, $dataSeriesValues);
        $layout = new Layout();
        $layout->setShowVal(true);
        $layout->setShowPercent(true);
        $plotArea = new PlotArea($layout, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $chart = new Chart('chart_demografi', new Title('Proporsi Pasien'), $legend, $plotArea, true, 0, null, null);

        $chart->setTopLeftPosition('E16');
        $chart->setBottomRightPosition('J30');
        $sheet->addChart($chart);

        // Chart 2: Stunting Balita Laki & Perempuan (Bar)
        $dataSeriesLabels2 = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Rekapitulasi!$F$5', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Rekapitulasi!$G$5', null, 1)
        ];
        $xAxisTickValues2 = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Rekapitulasi!$E$6:$E$9', null, 4)];
        $dataSeriesValues2 = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Rekapitulasi!$F$6:$F$9', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Rekapitulasi!$G$6:$G$9', null, 4)
        ];

        $series2 = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED, range(0, count($dataSeriesValues2) - 1), $dataSeriesLabels2, $xAxisTickValues2, $dataSeriesValues2);
        $series2->setPlotDirection(DataSeries::DIRECTION_COL);
        $plotArea2 = new PlotArea(null, [$series2]);
        $legend2 = new Legend(Legend::POSITION_RIGHT, null, false);
        $chart2 = new Chart('chart_stunting', new Title('Analisis Stunting Balita (TB/U)'), $legend2, $plotArea2, true, 0, null, null);

        $chart2->setTopLeftPosition('K4');
        $chart2->setBottomRightPosition('S18');
        $sheet->addChart($chart2);
    }

    private function createReferenceSheet(Spreadsheet $spreadsheet, $sheetName, $tableName, $keyCol)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($sheetName);
        $sheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        $data = DB::table($tableName)->get();

        $sheet->setCellValue('A1', 'Criteria_Val');
        $sheet->setCellValue('B1', 'Gender');
        $sheet->setCellValue('C1', 'Median');
        $sheet->setCellValue('D1', 'Neg1SD');
        $sheet->setCellValue('E1', 'Pos1SD');

        $row = 2;
        foreach ($data as $d) {
            $sheet->setCellValue("A$row", (float) $d->$keyCol);
            $sheet->setCellValue("B$row", $d->gender);
            $sheet->setCellValue("C$row", (float) $d->median);
            $sheet->setCellValue("D$row", (float) $d->neg1sd);
            $sheet->setCellValue("E$row", (float) $d->pos1sd);
            $row++;
        }
    }
}
