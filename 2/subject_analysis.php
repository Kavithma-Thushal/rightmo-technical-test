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

// Extract subject data
$subjects = ['Math', 'Science', 'English', 'History'];
$subjectScores = [
        'Math' => [],
        'Science' => [],
        'English' => [],
        'History' => []
];

$allScores = [];
$studentDetails = [];

for ($i = 1; $i < count($data); $i++) {
    $row = $data[$i];
    $index_no = $row[0];
    $name = $row[1] . " " . $row[2];

    $math = (int)$row[3];
    $science = (int)$row[4];
    $english = (int)$row[5];
    $history = (int)$row[6];

    // Store scores by subject
    $subjectScores['Math'][] = ['score' => $math, 'student' => $name, 'index' => $index_no];
    $subjectScores['Science'][] = ['score' => $science, 'student' => $name, 'index' => $index_no];
    $subjectScores['English'][] = ['score' => $english, 'student' => $name, 'index' => $index_no];
    $subjectScores['History'][] = ['score' => $history, 'student' => $name, 'index' => $index_no];

    // Store all individual scores
    $allScores[] = ['score' => $math, 'subject' => 'Math', 'student' => $name, 'index' => $index_no];
    $allScores[] = ['score' => $science, 'subject' => 'Science', 'student' => $name, 'index' => $index_no];
    $allScores[] = ['score' => $english, 'subject' => 'English', 'student' => $name, 'index' => $index_no];
    $allScores[] = ['score' => $history, 'subject' => 'History', 'student' => $name, 'index' => $index_no];
}

foreach ($subjects as $subject) {
    usort($subjectScores[$subject], function ($a, $b) {
        return $b['score'] - $a['score'];
    });

    $highest = $subjectScores[$subject][0];
    $lowest = $subjectScores[$subject][count($subjectScores[$subject]) - 1];

    $subjectStats[$subject] = [
            'highest' => $highest,
            'lowest' => $lowest
    ];
}

// Find highest and lowest
usort($allScores, function ($a, $b) {
    return $b['score'] - $a['score'];
});

$highestOverall = $allScores[0];
$lowestOverall = $allScores[count($allScores) - 1];

// HTML
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<h1>Highest & Lowest Scores For Each Subject</h1>

<?php foreach ($subjectStats as $subject => $stats): ?>
    <h2><?php echo htmlspecialchars($subject); ?></h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>Type</th>
            <th>Score</th>
            <th>Student</th>
            <th>Index No</th>
        </tr>
        <tr>
            <td>Highest</td>
            <td><?php echo $stats['highest']['score']; ?></td>
            <td><?php echo htmlspecialchars($stats['highest']['student']); ?></td>
            <td><?php echo htmlspecialchars($stats['highest']['index']); ?></td>
        </tr>
        <tr>
            <td>Lowest</td>
            <td><?php echo $stats['lowest']['score']; ?></td>
            <td><?php echo htmlspecialchars($stats['lowest']['student']); ?></td>
            <td><?php echo htmlspecialchars($stats['lowest']['index']); ?></td>
        </tr>
    </table>
<?php endforeach; ?>