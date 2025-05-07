<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: dashboard.php");
    exit();
}

$employee_id = $_SESSION['user_id'];

$applications = $conn->query("
    SELECT applications.*, jobs.title 
    FROM applications 
    JOIN jobs ON applications.job_id = jobs.job_id 
    WHERE employee_id = $employee_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Applications - MoroccoHire</title>
    <link rel="stylesheet" href="css/style.css"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .main-content {
            margin-left: 240px;
            padding: 30px;
        }
        .job-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
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
    <h2>📌 My Job Applications</h2>

    <?php if ($applications->num_rows > 0): ?>
        <?php while($app = $applications->fetch_assoc()): ?>
            <div class="job-card">
                <p><strong>Job:</strong> <?= htmlspecialchars($app['title']) ?></p>
                <p><strong>Status:</strong> <?= ucfirst($app['status']) ?></p>
                <p><strong>Date Applied:</strong> <?= $app['application_date'] ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert">You haven't applied for any jobs yet. Start exploring opportunities!</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn">⬅ Back to Dashboard</a>
</div>

<div class="footer">© 2025 MoroccoHire. All rights reserved.</div>

</body>
</html>
