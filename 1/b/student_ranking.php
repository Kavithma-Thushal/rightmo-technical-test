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

// Check if running from command line
if (php_sapi_name() !== 'cli') {
    die("This script must be run from command line.\n");
}

// Process data: Calculate totals and averages
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
usort($students, function($a, $b) {
    return $b['total'] - $a['total'];
});

// Add rank
for ($i = 0; $i < count($students); $i++) {
    $students[$i]['rank'] = $i + 1;
}


// Display data in console format
echo "\n";
echo "==================== STUDENT MARKS REPORT WITH RANKINGS ====================\n";
echo "\n";

// Display header
printf("%-6s %-10s %-20s %-6s %-8s %-8s %-8s %-7s %-8s %-15s\n",
    "Rank", "Index No", "Name", "Math", "Science", "English", "History", "Total", "Average", "Classification");
echo str_repeat("-", 120) . "\n";

// Display student data
foreach ($students as $student) {
    printf("%-6d %-10s %-20s %-6d %-8d %-8d %-8d %-7d %-8.2f %-15s\n",
        $student['rank'],
        $student['index_no'],
        $student['first_name'] . " " . $student['last_name'],
        $student['math'],
        $student['science'],
        $student['english'],
        $student['history'],
        $student['total'],
        $student['average'],
        $student['classification']
    );
}

echo str_repeat("-", 120) . "\n";
echo "\n";

// Summary Statistics
$totalSum = array_sum(array_column($students, 'total'));
$averageSum = array_sum(array_column($students, 'average'));
$highestTotal = max(array_column($students, 'total'));
$lowestTotal = min(array_column($students, 'total'));
$classAverage = $averageSum / count($students);

echo "======================= SUMMARY STATISTICS =======================\n";
echo "Total Students:        " . count($students) . "\n";
echo "Highest Total Marks:   " . $highestTotal . "\n";
echo "Lowest Total Marks:    " . $lowestTotal . "\n";
echo "Class Average:         " . number_format($classAverage, 2) . "\n";
echo "===================================================================\n";
echo "\n";