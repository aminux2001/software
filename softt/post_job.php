<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $requirements = $_POST['requirements'];
    $salary = $_POST['salary'];
    $company_id = $_SESSION['user_id'];

    $sql = "INSERT INTO jobs (company_id, title, description, requirements, salary) 
            VALUES ($company_id, '$title', '$description', '$requirements', '$salary')";

    if ($conn->query($sql) === TRUE) {
        $message = "✅ Job posted successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post a Job - MoroccoHire</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content {
            margin-left: 240px;
            padding: 30px;
        }
        form label {
            display: block;
            margin: 15px 0 5px;
        }
        form input[type="text"], form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        form input[type="submit"] {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="navbar">MoroccoHire Dashboard</div>

<div class="sidebar">
    <h3>Menu</h3>
    <a href="post_job.php">📄 Post a Job</a>
    <a href="manage_applications.php">📥 Manage Applications</a>
    <a href="profile.php">👤 Manage Profile</a>
    <a href="logout.php" style="color:#d10000;">🚪 Logout</a>
</div>

<div class="main-content">
    <h2 style="text-align:center;">📢 Post a New Job Offer</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?= $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Job Title:</label>
        <input type="text" name="title" required>

        <label>Description:</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Requirements:</label>
        <textarea name="requirements" rows="3"></textarea>

        <label>Salary:</label>
        <input type="text" name="salary">

        <input type="submit" value="Post Job" class="btn">
    </form>

    <a href="dashboard.php" class="btn" style="margin-top:20px; display:inline-block;">⬅ Back to Dashboard</a>
</div>

<div class="footer">© 2025 MoroccoHire. All rights reserved.</div>

</body>
</html>
