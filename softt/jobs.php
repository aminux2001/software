<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: dashboard.php");
    exit();
}

$jobs = $conn->query("SELECT jobs.*, users.username AS company_name FROM jobs 
                      JOIN users ON jobs.company_id = users.id 
                       ORDER BY posted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Jobs - MoroccoHire</title>
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
        .job-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
        .main-content {
            margin-left: 240px;
            padding: 30px;
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
    <h2>💼 Available Jobs</h2>

    <?php if ($jobs->num_rows > 0): ?>
        <?php while($job = $jobs->fetch_assoc()): ?>
            <div class="job-card">
                <h3><?= htmlspecialchars($job['title']) ?> <small>(<?= htmlspecialchars($job['company_name']) ?>)</small></h3>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($job['description'])) ?></p>
                <p><strong>Requirements:</strong> <?= nl2br(htmlspecialchars($job['requirements'])) ?></p>
                <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?></p>
                <form method="POST" action="apply.php">
                    <input type="hidden" name="job_id" value="<?= $job['job_id'] ?>">
                    <input type="submit" class="btn" value="Apply">
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert">❌ No job offers available at the moment. Please check back later!</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn">⬅ Back to Dashboard</a>
</div>

<div class="footer">© 2025 MoroccoHire. All rights reserved.</div>

</body>
</html>
