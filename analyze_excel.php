<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFileName = 'd:/PROJEK/LAPORAN ALSUS DAN ALSINTOR.xlsx';

try {
    if (!file_exists($inputFileName)) {
        die("File not found: " . $inputFileName);
    }
    $spreadsheet = IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, true);

    echo "--- STRUCTURE ---\n";
    // Check headers in row 1-5 to find where the actual table starts
    for ($i = 1; $i <= 5; $i++) {
        echo "Row $i: ";
        foreach (range('A', 'L') as $col) {
            $val = $sheet->getCell($col . $i)->getValue();
            echo "[$col: " . ($val ?: "") . "] ";
        }
        echo "\n";
    }

    echo "\n--- FORMULA CHECK ---\n";
    foreach (range(1, 15) as $row) {
        foreach (range('A', 'L') as $col) {
            $cell = $sheet->getCell($col . $row);
            if ($cell->isFormula()) {
                echo "Cell $col$row has formula: " . $cell->getValue() . " (Calculated: " . $cell->getOldCalculatedValue() . ")\n";
            }
        }
    }

} catch (\Exception $e) {
    echo 'Error loading file: ' . $e->getMessage();
}
