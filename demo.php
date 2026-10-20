<?php

$students = [
    [
        "student_name" => "Sample Student One",
        "year_level" => "2nd Year",
        "course" => "BSIT",
        "section" => "DEMO-01",
        "subject" => "ITE 300",
        "individual_work" => 88,
        "module_completion" => 92,
        "coding_assessment" => 85,
        "term_exam" => 90,
    ],
    [
        "student_name" => "Sample Student Two",
        "year_level" => "3rd Year",
        "course" => "BSCS",
        "section" => "DEMO-02",
        "subject" => "ITE 300",
        "individual_work" => 78,
        "module_completion" => 81,
        "coding_assessment" => 74,
        "term_exam" => 80,
    ],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Evaluator - Public Demo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>STUDENT EVALUATOR</h1>
        <p>Public demo — all records below are fictional sample data.</p>
    </div>

    <div class="box">
        <h2>Sample Student Records</h2>
        <?php foreach ($students as $student): ?>
            <?php
            $finalEvaluation = (
                $student["individual_work"] +
                $student["module_completion"] +
                $student["coding_assessment"] +
                $student["term_exam"]
            ) / 4;
            $result = $finalEvaluation >= 50 ? "PASSED" : "FAILED";
            ?>
            <div class="student-card">
                <div class="student-header">
                    <h3><?php echo htmlspecialchars($student["student_name"]); ?></h3>
                    <span class="<?php echo strtolower($result); ?>"><?php echo $result; ?></span>
                </div>
                <p><strong>Year Level:</strong> <?php echo htmlspecialchars($student["year_level"]); ?></p>
                <p><strong>Course:</strong> <?php echo htmlspecialchars($student["course"]); ?></p>
                <p><strong>Section:</strong> <?php echo htmlspecialchars($student["section"]); ?></p>
                <p><strong>Subject:</strong> <?php echo htmlspecialchars($student["subject"]); ?></p>
                <hr>
                <p>Individual Work: <strong><?php echo $student["individual_work"]; ?></strong></p>
                <p>Module Completion: <strong><?php echo $student["module_completion"]; ?></strong></p>
                <p>Coding Assessment: <strong><?php echo $student["coding_assessment"]; ?></strong></p>
                <p>Term Exam: <strong><?php echo $student["term_exam"]; ?></strong></p>
                <div class="evaluation">
                    Final Evaluation:
                    <strong><?php echo number_format($finalEvaluation, 2); ?></strong>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="footer">
        This public demo is read-only and contains no real student records.
    </div>
</div>
</body>
</html>
