<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($role == 'employee') {
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $resume = mysqli_real_escape_string($conn, $_POST['resume']);

        $check = $conn->query("SELECT * FROM profiles WHERE user_id = $user_id");
        if ($check->num_rows > 0) {
            $sql = "UPDATE profiles SET full_name='$full_name', resume='$resume' WHERE user_id=$user_id";
        } else {
            $sql = "INSERT INTO profiles (user_id, full_name, resume) VALUES ($user_id, '$full_name', '$resume')";
        }
    } else if ($role == 'company') {
        $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
        $company_info = mysqli_real_escape_string($conn, $_POST['company_info']);

        $check = $conn->query("SELECT * FROM profiles WHERE user_id = $user_id");
        if ($check->num_rows > 0) {
            $sql = "UPDATE profiles SET full_name='$company_name', company_info='$company_info' WHERE user_id=$user_id";
        } else {
            $sql = "INSERT INTO profiles (user_id, full_name, company_info) VALUES ($user_id, '$company_name', '$company_info')";
        }
    }

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert' style='background:#d4edda; color:#155724;'>✅ Profile saved successfully!</div>";
    } else {
        echo "<div class='alert' style='background:#f8d7da; color:#721c24;'>❌ Error: " . $conn->error . "</div>";
    }
}

$profile = $conn->query("SELECT * FROM profiles WHERE user_id = $user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Profile - MoroccoHire</title>
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
    <h2 style="text-align:center;">👤 Manage Your Profile</h2>

    <?php if ($role == 'employee'): ?>
        <form method="POST">
            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>" required>

            <label>Resume:</label>
            <textarea name="resume" rows="6" required><?= htmlspecialchars($profile['resume'] ?? '') ?></textarea>
            <small style="color:gray;">🧠 Tip: Add your skills and experience to get better job matches.</small>

            <input type="submit" value="Save Resume" class="btn">
        </form>

    <?php elseif ($role == 'company'): ?>
        <form method="POST">
            <label>Company Name:</label>
            <input type="text" name="company_name" value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>" required>

            <label>Company Info:</label>
            <textarea name="company_info" rows="6"><?= htmlspecialchars($profile['company_info'] ?? '') ?></textarea>

            <input type="submit" value="Save Company Info" class="btn">
        </form>
    <?php endif; ?>

    <a href="dashboard.php" class="btn" style="margin-top:20px;">⬅ Back to Dashboard</a>
</div>

<div class="footer">© 2025 MoroccoHire. All rights reserved.</div>

</body>
</html>