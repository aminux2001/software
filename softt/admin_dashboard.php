<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); 
    exit();
}

// Fetch Data
$users = $conn->query("SELECT * FROM users ORDER BY role");
$jobs = $conn->query("SELECT jobs.*, users.username AS company_name FROM jobs JOIN users ON jobs.company_id = users.id");
$applications = $conn->query("SELECT applications.*, jobs.title, u.username AS employee_name FROM applications JOIN jobs ON applications.job_id = jobs.job_id JOIN users u ON applications.employee_id = u.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - MoroccoHire</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="navbar">MoroccoHire Admin Panel</div>

    <div class="container">
        <h2>👥 All Users</h2>
        <table>
            <tr>
                <th>ID</th><th>Username</th><th>Email</th><th>Role</th>
            </tr>
            <?php while($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= ucfirst($user['role']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h2>💼 All Job Posts</h2>
        <table>
            <tr>
                <th>ID</th><th>Title</th><th>Company</th><th>Posted At</th>
            </tr>
            <?php while($job = $jobs->fetch_assoc()): ?>
                <tr>
                    <td><?= $job['job_id'] ?></td>
                    <td><?= htmlspecialchars($job['title']) ?></td>
                    <td><?= htmlspecialchars($job['company_name']) ?></td>
                    <td><?= $job['posted_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h2>📄 All Applications</h2>
        <table>
            <tr>
                <th>ID</th><th>Employee</th><th>Job Title</th><th>Status</th><th>Date</th>
            </tr>
            <?php while($app = $applications->fetch_assoc()): ?>
                <tr>
                    <td><?= $app['application_id'] ?></td>
                    <td><?= htmlspecialchars($app['employee_name']) ?></td>
                    <td><?= htmlspecialchars($app['title']) ?></td>
                    <td><?= ucfirst($app['status']) ?></td>
                    <td><?= $app['application_date'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <a href="logout.php" class="btn" style="background-color:#e60023; margin-top:20px;">Logout</a>
    </div>

    <div class="footer">© 2025 MoroccoHire Admin. All rights reserved.</div>

</body>
</html>
