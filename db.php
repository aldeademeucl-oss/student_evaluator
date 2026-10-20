<?php

$conn = new mysqli("localhost", "root", "", "student_evaluator");

if ($conn->connect_error) {
    http_response_code(500);
    die("Database Connection Failed: " . $conn->connect_error . 
        "<br><br><p>Please initialize the database by visiting: " .
        "<a href='init_database.php'>init_database.php</a></p>");
}

?>