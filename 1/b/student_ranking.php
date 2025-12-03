<?php

// Load CSV
$csv = '../../student_marks.csv';

// Read CSV
$file = fopen($csv, 'r');
$data = [];

while (($row = fgetcsv($file)) !== false) {
    $data[] = $row;
}

// Close CSV
fclose($file);

// Detect if running via web or CLI
$is_web = (php_sapi_name() !== 'cli');

// Calculate totals and averages
$students = [];

for ($i = 1; $i < count($data); $i++) {
    $row = $data[$i];

    $math = (int)$row[3];
    $science = (int)$row[4];
    $english = (int)$row[5];
    $history = (int)$row[6];

    $total = $math + $science + $english + $history;
    $average = $total / 4;

    // Classify based on average
    if ($average >= 75) {
        $classification = 'Distinction';
    } elseif ($average >= 60) {
        $classification = 'Credit';
    } elseif ($average >= 50) {
        $classification = 'Pass';
    } else {
        $classification = 'Fail';
    }

    $students[] = [
        'index_no' => $row[0],
        'first_name' => $row[1],
        'last_name' => $row[2],
        'math' => $math,
        'science' => $science,
        'english' => $english,
        'history' => $history,
        'total' => $total,
        'average' => $average,
        'classification' => $classification
    ];
}

// Sort students by total marks in descending order
usort($students, function ($a, $b) {
    return $b['total'] - $a['total'];
});

// Add rank
for ($i = 0; $i < count($students); $i++) {
    $students[$i]['rank'] = $i + 1;
}

if ($is_web) {
    // Output
    echo "<h1>Student Rankings</h1>\n";
    echo "<table border=1 cellpadding=5>\n";
    echo "<tr><th>Rank</th><th>Index No</th><th>Name</th><th>Math</th><th>Science</th><th>English</th><th>History</th><th>Total</th><th>Average</th><th>Classification</th></tr>\n";
    foreach ($students as $student) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($student['rank']) . "</td>";
        echo "<td>" . htmlspecialchars($student['index_no']) . "</td>";
        echo "<td>" . htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($student['math']) . "</td>";
        echo "<td>" . htmlspecialchars($student['science']) . "</td>";
        echo "<td>" . htmlspecialchars($student['english']) . "</td>";
        echo "<td>" . htmlspecialchars($student['history']) . "</td>";
        echo "<td>" . htmlspecialchars($student['total']) . "</td>";
        echo "<td>" . htmlspecialchars(number_format($student['average'], 2)) . "</td>";
        echo "<td>" . htmlspecialchars($student['classification']) . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
}