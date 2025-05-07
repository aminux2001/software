<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

$accepted_count = 0;
if ($role == 'employee') {
    $employee_id = $_SESSION['user_id'];
    $notif_check = $conn->query("SELECT COUNT(*) AS accepted_count FROM applications WHERE employee_id = $employee_id AND status = 'accepted'");
    $notif = $notif_check->fetch_assoc();
    $accepted_count = $notif['accepted_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - MoroccoHire</title>
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
</head>
<body>
    <div class="navbar">MoroccoHire Dashboard</div>

    <div class="sidebar">
        <h3>Menu</h3>
        <?php if ($role == 'company'): ?>
            <a href="post_job.php">📄 Post a Job</a>
            <a href="manage_applications.php">📥 Manage Applications</a>
        <?php else: ?>
            <a href="jobs.php">💼 View Jobs</a>
            <a href="my_applications.php">📌 My Applications</a>
            <a href="recommend_jobs.php">🤖 Recommended Jobs</a>
        <?php endif; ?>
        <a href="profile.php">👤 Manage Profile</a>
        <a href="logout.php" style="color:#d10000;">🚪 Logout</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>Welcome, <?= ucfirst($role); ?>!</h2>
            <?php if ($role == 'employee' && $accepted_count > 0): ?>
                <div class="alert" style="background-color:#d4edda; color:#155724; border: 1px solid #c3e6cb;">
                    🎉 You have <?= $accepted_count ?> accepted application<?= $accepted_count > 1 ? 's' : '' ?>! <a href="my_applications.php">View</a>
                </div>
            <?php endif; ?>
            <p>Here's a quick snapshot of your profile and job activity.</p>
        </div>

        <?php if ($role == 'employee'): ?>
            <div class="card">
                <h3>🔍 AI Skill Highlights</h3>
                <canvas id="miniAIChart" height="200"></canvas>
            </div>
            <div class="card">
                <h3>📰 AI-Based Opportunities</h3>
                <p>Check <a href="recommend_jobs.php">Recommended Jobs</a> based on your skills.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">© 2025 MoroccoHire. All rights reserved.</div>

    <?php if ($role == 'employee'): ?>
    <script>
        const aiSkillScores = {
            "PHP": 85,
            "Laravel": 75,
            "MySQL": 80,
            "JavaScript": 90,
            "Python": 70
        };

        const ctxMini = document.getElementById('miniAIChart').getContext('2d');
        new Chart(ctxMini, {
            type: 'bar',
            data: {
                labels: Object.keys(aiSkillScores),
                datasets: [{
                    label: 'AI Skill Match %',
                    data: Object.values(aiSkillScores),
                    backgroundColor: '#0a66c2'
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
