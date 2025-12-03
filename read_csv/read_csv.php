<?php

// Load CSV
$csv = '../student_marks.csv';

// Read CSV
$file = fopen($csv, 'r');
$data = [];

while (($row = fgetcsv($file)) !== false) {
    $data[] = $row;
}

// Close CSV
fclose($file);

// Display data
echo "<table border='1' cellpadding='5'>";

// Display header
echo "<tr>";
foreach ($data[0] as $header) {
    echo "<th>$header</th>";
}
echo "</tr>";

// Display data rows
for ($i = 1; $i < count($data); $i++) {
    echo "<tr>";
    foreach ($data[$i] as $cell) {
        echo "<td>$cell</td>";
    }
    echo "</tr>";
}

echo "</table>";