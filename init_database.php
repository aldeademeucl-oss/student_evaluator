<?php
/*
    Database initialization script
    Creates the student_evaluator database and students table
*/

$servername = "localhost";
$username = "root";
$password = "";

// Create connection without database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS student_evaluator";
if ($conn->query($sql) === TRUE) {
    echo "Database 'student_evaluator' created successfully or already exists.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db("student_evaluator");

// Create students table
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(255) NOT NULL,
    year_level VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    section VARCHAR(100) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    individual_work DECIMAL(5, 2) NOT NULL,
    module_completion DECIMAL(5, 2) NOT NULL,
    coding_assessment DECIMAL(5, 2) NOT NULL,
    term_exam DECIMAL(5, 2) NOT NULL,
    final_evaluation DECIMAL(5, 2) NOT NULL,
    result VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'students' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Create staff_users table if not exists
$sql = "CREATE TABLE IF NOT EXISTS staff_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'staff_users' created successfully or already exists.<br>";
} else {
    die("Error creating staff_users table: " . $conn->error);
}

echo "<p style='color: green; font-weight: bold;'>Database initialization completed successfully!</p>";
echo "<p><a href='setup_staff.php'>Go to Staff Setup</a> | <a href='demo.php'>View Demo</a></p>";

$conn->close();
?>
