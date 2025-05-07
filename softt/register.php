<?php
include('config/db.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];  // employee or company

    $sql = "INSERT INTO users (username, password, email, role) 
            VALUES ('$username', '$password', '$email', '$role')";

    if ($conn->query($sql) === TRUE) {
        $message = "✅ Registration successful! <a href='login.php'>Login here</a>";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MoroccoHire</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="navbar">MoroccoHire - Register</div>

    <div class="container" style="max-width:400px;">
        <h2 style="text-align:center;">Create Your Account</h2>

        <?php if (!empty($message)): ?>
            <div class="alert"><?= $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Account type:</label>
            <select name="role" required>
                <option value="employee">Employee</option>
                <option value="company">Company</option>
            </select>

            <input type="submit" value="Register">
        </form>

        <p style="text-align:center; margin-top:15px;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>

    <div class="footer">© 2025 MoroccoHire. All rights reserved.</div>

</body>
</html>
