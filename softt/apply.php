<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: dashboard.php");
    exit();
}

$role = $_SESSION['role'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = $_POST['job_id'];
    $employee_id = $_SESSION['user_id'];

    $check = $conn->query("SELECT * FROM applications WHERE job_id=$job_id AND employee_id=$employee_id");
    if ($check->num_rows > 0) {
        $message = "⚠️ You have already applied for this job.";
    } else {
        $sql = "INSERT INTO applications (job_id, employee_id) VALUES ($job_id, $employee_id)";
        if ($conn->query($sql) === TRUE) {
            $message = "✅ Application submitted successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Status - MoroccoHire</title>
    <link rel="stylesheet" href="css/style.css">
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f2ef;
            margin: 0;
        }
        .navbar {
            background-color: #283e4a;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
        .sidebar {
            width: 220px;
            background: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            border-right: 1px solid #ddd;
        }
        .sidebar a {
            display: block;
            margin: 15px 0;
            color: #0a66c2;
            text-decoration: none;
            font-weight: 600;
        }
        .main-content {
            margin-left: 240px;
            padding: 30px;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 18px;
            border: none;
            background: #0a66c2;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #888;
            background: white;
            border-top: 1px solid #ddd;
            margin-top: 40px;
        }
    </style>
    <style>
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="navbar">MoroccoHire Dashboard</div>

    <div class="sidebar">
        <h3>Menu</h3>
        <a href="jobs.php">💼 View Jobs</a>
        <a href="my_applications.php">📌 My Applications</a>
        <a href="recommend_jobs.php">🤖 Recommended Jobs</a>
        <a href="profile.php">👤 Manage Profile</a>
        <a href="logout.php" style="color:#d10000;">🚪 Logout</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>📩 Application Status</h2>
            <?php if (!empty($message)): ?>
                <div class="alert"><?= $message; ?></div>
            <?php endif; ?>
            <a href="jobs.php" class="btn">⬅ Back to Jobs</a>
        </div>
    </div>

    <div class="footer">© 2025 MoroccoHire. All rights reserved.</div>
</body>
</html>
