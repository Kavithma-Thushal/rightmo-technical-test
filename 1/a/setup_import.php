<?php


// Database configuration
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "student_marks";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db($database);

// Create Students table
$sql_students = "CREATE TABLE IF NOT EXISTS students (
    index_no INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";

if ($conn->query($sql_students) === TRUE) {
    echo "Students table created successfully<br>";
} else {
    echo "Error creating students table: " . $conn->error . "<br>";
}

// Create Subjects table
$sql_subjects = "CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
)";

if ($conn->query($sql_subjects) === TRUE) {
    echo "Subjects table created successfully<br>";
} else {
    echo "Error creating subjects table: " . $conn->error . "<br>";
}

// Create Marks table
$sql_marks = "CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    marks INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(index_no),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
)";

if ($conn->query($sql_marks) === TRUE) {
    echo "Marks table created successfully<br>";
} else {
    echo "Error creating marks table: " . $conn->error . "<br>";
}

echo "<br>===== Starting Data Import =====<br>";

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

// Extract subjects from header (skip first 3 columns: index_no, first_name, last_name)
$subjects = [];
for ($i = 3; $i < count($data[0]); $i++) {
    $subjects[] = $data[0][$i];
}

// Insert subjects
foreach ($subjects as $subject) {
    $subject = $conn->real_escape_string($subject);
    $sql = "INSERT IGNORE INTO subjects (name) VALUES ('$subject')";
    $conn->query($sql);
}

echo "Subjects inserted successfully<br>";

// Insert students and marks
for ($i = 1; $i < count($data); $i++) {
    $index_no = $conn->real_escape_string($data[$i][0]);
    $first_name = $conn->real_escape_string($data[$i][1]);
    $last_name = $conn->real_escape_string($data[$i][2]);
    $full_name = $first_name . " " . $last_name;

    // Insert student
    $sql = "INSERT IGNORE INTO students (index_no, name) VALUES ('$index_no', '$full_name')";
    $conn->query($sql);

    // Insert marks for each subject
    for ($j = 3; $j < count($data[$i]); $j++) {
        $subject_name = $data[0][$j];
        $mark = (int)$data[$i][$j];

        // Get subject_id
        $subject_sql = "SELECT id FROM subjects WHERE name = '$subject_name'";
        $result = $conn->query($subject_sql);
        $subject_row = $result->fetch_assoc();
        $subject_id = $subject_row['id'];

        // Insert mark
        $sql = "INSERT INTO marks (student_id, subject_id, marks) VALUES ('$index_no', '$subject_id', '$mark')";
        if ($conn->query($sql) !== TRUE) {
            echo "Error inserting mark: " . $conn->error . "<br>";
        }
    }
}

echo "All records imported successfully!<br>";

// Display summary
echo "<br>===== Data Summary =====<br>";

// Count students
$result = $conn->query("SELECT COUNT(*) as count FROM students");
$row = $result->fetch_assoc();
echo "Total Students: " . $row['count'] . "<br>";

// Count subjects
$result = $conn->query("SELECT COUNT(*) as count FROM subjects");
$row = $result->fetch_assoc();
echo "Total Subjects: " . $row['count'] . "<br>";

// Count marks
$result = $conn->query("SELECT COUNT(*) as count FROM marks");
$row = $result->fetch_assoc();
echo "Total Marks Records: " . $row['count'] . "<br>";

// Close connection
$conn->close();