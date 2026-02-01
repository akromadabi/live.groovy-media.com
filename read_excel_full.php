<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__ . '/BASE.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray(null, true, true, true);

echo "Total rows: " . count($data) . "\n\n";

// Show first 10 rows to understand structure
echo "First 10 rows:\n";
for ($i = 1; $i <= 10; $i++) {
    if (isset($data[$i])) {
        echo "Row $i: " . json_encode($data[$i], JSON_UNESCAPED_UNICODE) . "\n";
    }
}

// Show unique names
$names = [];
foreach ($data as $rowNum => $row) {
    if ($rowNum <= 1)
        continue;
    $name = trim($row['A'] ?? '');
    if (!empty($name)) {
        $names[$name] = ($names[$name] ?? 0) + 1;
    }
}

echo "\n\nUnique names and their attendance count:\n";
foreach ($names as $name => $count) {
    echo "$name: $count records\n";
}
