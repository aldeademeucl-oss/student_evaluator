<?php

require_once "auth.php";
require_admin_login();
require_once "db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_is_valid()) {
        http_response_code(400);
        exit("Invalid form submission. Refresh the page and try again.");
    }

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
            "INSERT INTO staff_users (username, password_hash, role) VALUES (?, ?, 'staff')"
        );
        $stmt->bind_param("ss", $username, $passwordHash);

        if ($stmt->execute()) {
            $message = "Staff account created successfully.";
        } elseif ($stmt->errno === 1062) {
            $error = "That username is already in use. Choose another.";
        } else {
            throw new RuntimeException("Could not create staff account: " . $stmt->error);
        }

        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff Account - Student Evaluator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>ADD STAFF ACCOUNT</h1>
        <p>Create a separate sign-in for another staff member.</p>
    </div>
    <div class="box">
        <?php if ($error !== ""): ?>
            <div class="message" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($message !== ""): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
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
        <p><a href="index.php">Back to student records</a></p>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
