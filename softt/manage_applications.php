<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: dashboard.php");
    exit();
}

$company_id = $_SESSION['user_id'];

// Handle Approve/Reject Action
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $app_id = $_POST['application_id'];
    $action = $_POST['action'];  // 'accepted' or 'rejected'

    $conn->query("UPDATE applications SET status='$action' WHERE application_id = $app_id");
}

// Fetch Applications for Company's Jobs
$applications = $conn->query("
    SELECT applications.application_id, applications.status, applications.application_date, 
           jobs.title, users.username AS employee_name
    FROM applications
    JOIN jobs ON applications.job_id = jobs.job_id
    JOIN users ON applications.employee_id = users.id
    WHERE jobs.company_id = $company_id
    ORDER BY applications.application_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Applications - MoroccoHire</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .btn {
            padding: 6px 12px;
            background-color: #0a66c2;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-danger {
            background-color: #e60023;
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
    <h2>📥 Applications to Your Job Offers</h2>

    <?php if ($applications->num_rows > 0): ?>
        <table>
            <tr>
                <th>Candidate</th>
                <th>Job Title</th>
                <th>Status</th>
                <th>Applied On</th>
                <th>Action</th>
            </tr>
            <?php while($app = $applications->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($app['employee_name']) ?></td>
                    <td><?= htmlspecialchars($app['title']) ?></td>
                    <td><?= ucfirst($app['status']) ?></td>
                    <td><?= $app['application_date'] ?></td>
                    <td>
                        <?php if ($app['status'] == 'pending'): ?>
                            <form method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="application_id" value="<?= $app['application_id'] ?>">
                                <button type="submit" name="action" value="accepted" class="btn">Approve</button>
                                <button type="submit" name="action" value="rejected" class="btn btn-danger">Reject</button>
                            </form>
                        <?php else: ?>
                            <em>No Action</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="alert">No applications received yet.</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn">⬅ Back to Dashboard</a>
</div>

<div class="footer">© 2025 MoroccoHire. All rights reserved.</div>

</body>
</html>
