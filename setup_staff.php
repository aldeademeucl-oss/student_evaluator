<?php

require_once "auth.php";
require_once "db.php";

$remoteAddress = $_SERVER["REMOTE_ADDR"] ?? "";
if (!in_array($remoteAddress, ["127.0.0.1", "::1"], true)) {
    http_response_code(403);
    exit("Staff setup is available only from this computer. Open http://localhost/Student_evaluator/setup_staff.php.");
}

$conn->query(
    "CREATE TABLE IF NOT EXISTS staff_users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
);

$staffCount = $conn->query("SELECT COUNT(*) AS total FROM staff_users")->fetch_assoc()["total"];
$message = "";
$error = "";

if ((int) $staffCount > 0) {
    $message = "Staff account setup is already complete. Sign in to continue.";
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_is_valid()) {
        $error = "Your session expired. Please try again.";
    } else {
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        if (!preg_match('/^[A-Za-z0-9_.-]{3,100}$/', $username)) {
            $error = "Use a username of 3–100 letters, numbers, dots, dashes, or underscores.";
        } elseif (strlen($password) < 12) {
            $error = "Choose a password with at least 12 characters.";
        } elseif ($password !== $confirmPassword) {
            $error = "The passwords do not match.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "INSERT INTO staff_users (username, password_hash, role) VALUES (?, ?, 'admin')"
            );
            $stmt->bind_param("ss", $username, $passwordHash);

            if ($stmt->execute()) {
                $message = "Staff account created. Setup is now locked. Sign in to continue.";
            } else {
                throw new RuntimeException("Could not create the staff account: " . $stmt->error);
            }

            $stmt->close();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initial Staff Setup - Student Evaluator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>INITIAL STAFF SETUP</h1>
        <p>Create the first staff sign-in for student records.</p>
    </div>
    <div class="box">
        <?php if ($error !== ""): ?>
            <div class="message" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($message !== ""): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <p><a href="login.php">Go to staff login</a></p>
        <?php elseif ((int) $staffCount === 0): ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <label for="username">Staff username</label>
                <input id="username" type="text" name="username" minlength="3" maxlength="100" autocomplete="username" required>
                <label for="password">Password (at least 12 characters)</label>
                <input id="password" type="password" name="password" minlength="12" autocomplete="new-password" required>
                <label for="confirm-password">Confirm password</label>
                <input id="confirm-password" type="password" name="confirm_password" minlength="12" autocomplete="new-password" required>
                <button type="submit">CREATE STAFF ACCOUNT</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
