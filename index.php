<?php

require_once "auth.php";
require_staff_login();
require_once "db.php";

$message = "";

/*
    ADD STUDENT
*/

if (isset($_POST["add"])) {

    if (!csrf_is_valid()) {
        http_response_code(400);
        exit("Invalid form submission. Refresh the page and try again.");
    }

    $studentName = $_POST["studentName"];
    $yearLevel = $_POST["yearLevel"];
    $course = $_POST["course"];
    $section = $_POST["section"];
    $subject = $_POST["subject"];

    $individualWork = $_POST["individualWork"];
    $moduleCompletion = $_POST["moduleCompletion"];
    $codingAssessment = $_POST["codingAssessment"];
    $termExam = $_POST["termExam"];

    /*
        Calculate final evaluation
    */

    $finalEvaluation = (
        $individualWork +
        $moduleCompletion +
        $codingAssessment +
        $termExam
    ) / 4;

    /*
        Determine result
    */

    if ($finalEvaluation >= 50) {
        $result = "PASSED";
    } else {
        $result = "FAILED";
    }

    /*
        Insert into database
    */

    $sql = "INSERT INTO students
    (
        student_name,
        year_level,
        course,
        section,
        subject,
        individual_work,
        module_completion,
        coding_assessment,
        term_exam,
        final_evaluation,
        result
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssddddds",
        $studentName,
        $yearLevel,
        $course,
        $section,
        $subject,
        $individualWork,
        $moduleCompletion,
        $codingAssessment,
        $termExam,
        $finalEvaluation,
        $result
    );

    if ($stmt->execute()) {
        $message = "Student added successfully.";
    } else {
        $message = "Error adding student.";
    }

    $stmt->close();
}


/*
    DELETE STUDENT
*/

if (isset($_POST["delete"])) {

    if (!csrf_is_valid()) {
        http_response_code(400);
        exit("Invalid delete request. Refresh the page and try again.");
    }

    $id = intval($_POST["delete"]);

    $sql = "DELETE FROM students WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $stmt->close();

    header("Location: index.php");

    exit();
}


/*
    GET STUDENTS
*/

$sql = "SELECT * FROM students ORDER BY id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Student Evaluator</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>

<div class="container">

    <div class="header">

        <h1>STUDENT EVALUATOR</h1>

        <p>
            Signed in as <?php echo htmlspecialchars($_SESSION["staff_username"] ?? "staff"); ?>
        </p>
        <div class="header-actions">
            <?php if (($_SESSION["staff_role"] ?? "") === "admin"): ?>
                <a class="link-button" href="add_staff.php">ADD STAFF</a>
            <?php endif; ?>
            <form method="POST" action="logout.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <button type="submit">SIGN OUT</button>
            </form>
        </div>

    </div>


    <?php if ($message != ""): ?>

        <div class="message">

            <?php echo $message; ?>

        </div>

    <?php endif; ?>


    <div class="box">

        <h2>Student Information</h2>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

            <label>Student Name</label>

            <input
                type="text"
                name="studentName"
                placeholder="Enter student name"
                required
            >


            <label>Year Level</label>

            <select
                name="yearLevel"
                required
            >

                <option value="">
                    Select Year Level
                </option>

                <option value="1st Year">
                    1st Year
                </option>

                <option value="2nd Year">
                    2nd Year
                </option>

                <option value="3rd Year">
                    3rd Year
                </option>

                <option value="4th Year">
                    4th Year
                </option>

            </select>


            <label>Course</label>

            <input
                type="text"
                name="course"
                placeholder="Example: BSIT"
                required
            >


            <label>Section</label>

            <input
                type="text"
                name="section"
                placeholder="Example: BSIT2-02"
                required
            >


            <label>Subject</label>

            <input
                type="text"
                name="subject"
                placeholder="Enter subject"
                required
            >


            <h2>Evaluation Scores</h2>


            <label>Individual Work</label>

            <input
                type="number"
                name="individualWork"
                min="0"
                max="100"
                step="0.01"
                required
            >


            <label>Module Completion</label>

            <input
                type="number"
                name="moduleCompletion"
                min="0"
                max="100"
                step="0.01"
                required
            >


            <label>Coding Assessment</label>

            <input
                type="number"
                name="codingAssessment"
                min="0"
                max="100"
                step="0.01"
                required
            >


            <label>Term Exam</label>

            <input
                type="number"
                name="termExam"
                min="0"
                max="100"
                step="0.01"
                required
            >


            <button
                type="submit"
                name="add"
            >
                ADD STUDENT
            </button>

        </form>

    </div>


    <div class="box">

        <h2>Student Records</h2>


        <?php if ($result->num_rows > 0): ?>

            <?php while ($student = $result->fetch_assoc()): ?>

                <div class="student-card">

                    <div class="student-header">

                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $student["student_name"]
                            );
                            ?>

                        </h3>


                        <?php if ($student["result"] == "PASSED"): ?>

                            <span class="passed">
                                PASSED
                            </span>

                        <?php else: ?>

                            <span class="failed">
                                FAILED
                            </span>

                        <?php endif; ?>

                    </div>


                    <p>
                        <strong>Year Level:</strong>

                        <?php
                        echo htmlspecialchars(
                            $student["year_level"]
                        );
                        ?>
                    </p>


                    <p>
                        <strong>Course:</strong>

                        <?php
                        echo htmlspecialchars(
                            $student["course"]
                        );
                        ?>
                    </p>


                    <p>
                        <strong>Section:</strong>

                        <?php
                        echo htmlspecialchars(
                            $student["section"]
                        );
                        ?>
                    </p>


                    <p>
                        <strong>Subject:</strong>

                        <?php
                        echo htmlspecialchars(
                            $student["subject"]
                        );
                        ?>
                    </p>


                    <hr>


                    <p>
                        Individual Work:

                        <strong>
                            <?php
                            echo $student["individual_work"];
                            ?>
                        </strong>
                    </p>


                    <p>
                        Module Completion:

                        <strong>
                            <?php
                            echo $student["module_completion"];
                            ?>
                        </strong>
                    </p>


                    <p>
                        Coding Assessment:

                        <strong>
                            <?php
                            echo $student["coding_assessment"];
                            ?>
                        </strong>
                    </p>


                    <p>
                        Term Exam:

                        <strong>
                            <?php
                            echo $student["term_exam"];
                            ?>
                        </strong>
                    </p>


                    <div class="evaluation">

                        Final Evaluation:

                        <strong>
                            <?php
                            echo number_format(
                                $student["final_evaluation"],
                                2
                            );
                            ?>
                        </strong>

                    </div>


                    <form method="POST" onsubmit="return confirm('Delete this student?')">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                        <input type="hidden" name="delete" value="<?php echo (int) $student["id"]; ?>">
                        <button class="delete" type="submit">DELETE</button>
                    </form>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <p class="no-data">
                No student records yet.
            </p>

        <?php endif; ?>

    </div>


    <div class="footer">

        PHP + MySQL

    </div>

</div>

</body>

</html>

<?php

$conn->close();

?>