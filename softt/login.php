<?php
session_start();
include('config/db.php');

// Initialize message variable
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Role-based redirection
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'company':
                case 'employee':
                    header("Location: dashboard.php");
                    break;
                default:
                    $message = "⚠️ Unknown user role.";
            }
            exit();
        } else {
            $message = "❌ Incorrect password.";
        }
    } else {
        $message = "⚠️ Username not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - MoroccoHire</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="navbar">MoroccoHire</div>

    <div class="container" style="max-width:400px;">
        <h2 style="text-align:center;">Login to Your Account</h2>

        <?php if (!empty($message)): ?>
            <div class="alert"><?= $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">
        </form>

        <p style="text-align:center; margin-top:15px;">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>

    <div class="footer">© 2025 MoroccoHire. All rights reserved.</div>

</body>
</html>
