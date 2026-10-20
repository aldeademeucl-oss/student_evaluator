<?php

require_once "auth.php";
require_once "db.php";

if (!empty($_SESSION["staff_id"])) {
    header("Location: index.php");
    exit();
}

$error = "";
$setupRequired = true;
$tableResult = $conn->query("SHOW TABLES LIKE 'staff_users'");

if ($tableResult && $tableResult->num_rows > 0) {
    $staffCount = $conn->query(
        "SELECT COUNT(*) AS total FROM staff_users"
    )->fetch_assoc()["total"];
    $setupRequired = (int) $staffCount === 0;
}

$creationError = "";
$creationMessage = "";

if ($setupRequired && $_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "create_staff") {
    if (!csrf_is_valid()) {
        $creationError = "Your session expired. Please try again.";
    } else {
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        if (!preg_match('/^[A-Za-z0-9_.-]{3,100}$/', $username)) {
            $creationError = "Use a username of 3–100 letters, numbers, dots, dashes, or underscores.";
        } elseif (strlen($password) < 12) {
            $creationError = "Choose a password with at least 12 characters.";
        } elseif ($password !== $confirmPassword) {
            $creationError = "The passwords do not match.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "INSERT INTO staff_users (username, password_hash, role) VALUES (?, ?, 'admin')"
            );
            $stmt->bind_param("ss", $username, $passwordHash);

            if ($stmt->execute()) {
                $creationMessage = "Staff account created successfully. You can now sign in below.";
                $setupRequired = false;
            } elseif ($stmt->errno === 1062) {
                $creationError = "That username is already in use. Choose another.";
            } else {
                throw new RuntimeException("Could not create staff account: " . $stmt->error);
            }

            $stmt->close();
        }
    }
}

if (!$setupRequired && $_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") !== "create_staff") {
    if (!csrf_is_valid()) {
        $error = "Your session expired. Please try again.";
    } else {
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        $stmt = $conn->prepare(
            "SELECT id, username, password_hash, role FROM staff_users WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $staff = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($staff && password_verify($password, $staff["password_hash"])) {
            session_regenerate_id(true);
            $_SESSION["staff_id"] = $staff["id"];
            $_SESSION["staff_username"] = $staff["username"];
            $_SESSION["staff_role"] = $staff["role"];
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
            header("Location: index.php");
            exit();
        }

        $error = "Invalid username or password.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - Student Evaluator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>STAFF LOGIN</h1>
        <p>Sign in to access student records.</p>
    </div>
    <div class="box">
        <?php if ($setupRequired): ?>
            <?php if ($creationError !== ""): ?>
                <div class="message error-message" role="alert"><?php echo htmlspecialchars($creationError); ?></div>
            <?php endif; ?>
            <?php if ($creationMessage !== ""): ?>
                <div class="message"><?php echo htmlspecialchars($creationMessage); ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="action" value="create_staff">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <label for="username">Staff username</label>
                <input id="username" type="text" name="username" minlength="3" maxlength="100" autocomplete="username" required>
                <label for="password">Password (at least 12 characters)</label>
                <input id="password" type="password" name="password" minlength="12" autocomplete="new-password" required>
                <label for="confirm-password">Confirm password</label>
                <input id="confirm-password" type="password" name="confirm_password" minlength="12" autocomplete="new-password" required>
                <button type="submit">CREATE STAFF ACCOUNT</button>
            </form>
            <p>Already created an account? <a href="login.php">Refresh and sign in</a></p>
        <?php else: ?>
            <?php if ($creationMessage !== ""): ?>
                <div class="message"><?php echo htmlspecialchars($creationMessage); ?></div>
            <?php endif; ?>
            <?php if ($error !== ""): ?>
                <div class="message error-message" role="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" autocomplete="username" required>
                <label for="password">Password</label>
                <input id="password" type="password" name="password" autocomplete="current-password" required>
                <button type="submit">SIGN IN</button>
            </form>
        <?php endif; ?>
    </div>
    <div class="footer">Authorized staff only.</div>
</div>
</body>
</html>
<?php $conn->close(); ?>
