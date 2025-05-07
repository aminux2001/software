<?php
session_start();
include('config/db.php');
include('config/ai.php');
include('matchmaker.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$resumeQuery = $conn->query("SELECT resume FROM profiles WHERE user_id = $user_id");

if (!$resumeQuery || $resumeQuery->num_rows === 0) {
    $error = "No resume found in your profile.";
} else {
    $resumeRow = $resumeQuery->fetch_assoc();
    $resume = $resumeRow['resume'] ?? '';

    if (!$resume) {
        $error = "Resume is empty.";
    } else {
        $jobQuery = $conn->query("SELECT job_id, title, description, requirements FROM jobs");
        $jobs = [];
        while ($job = $jobQuery->fetch_assoc()) {
            $jobs[] = $job;
        }

        if (count($jobs) === 0) {
            $error = "No jobs available in the system.";
        } else {
            $jobs = array_slice($jobs, 0, 10);
            $rawResponse = getJobRecommendations($resume, $jobs);

            if (empty($rawResponse)) {
                $error = "No response from AI scoring system.";
            } else {
                $rankings = [];
                foreach (explode("\n", trim($rawResponse)) as $line) {
                    if (preg_match('/^(\d+)\s*-\s*(\d+)/', $line, $match)) {
                        $rankings[$match[1]] = (int)$match[2];
                    }
                }

                usort($jobs, function ($a, $b) use ($rankings) {
                    return ($rankings[$b['job_id']] ?? 0) - ($rankings[$a['job_id']] ?? 0);
                });
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Job Matches - MoroccoHire AI</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .main-content { margin-left: 240px; padding: 30px; }
        .meter { height: 10px; background: #eee; border-radius: 6px; overflow: hidden; margin: 8px 0; }
        .meter span { display: block; height: 100%; background: #0a66c2; transition: width 0.5s ease; }
        .stars { color: #f1c40f; font-size: 18px; }
        .match-reason { color: gray; font-size: 14px; margin-top: 6px; }
        #skillsChart { background: #fff; padding: 10px; border-radius: 10px; }
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
    <h2>📌 Personalized Job Matches Powered by AI</h2>
    <p style="color:gray; font-size: 15px;">Our internal AI engine compares your resume against company job posts using hundreds of technical keywords, experience context, and job role relevance — delivering accurate, ranked job suggestions.</p>

    <?php if (!empty($error)): ?>
        <div class="alert">⚠️ <?= $error ?></div>
    <?php elseif (!empty($rankings)): ?>
        <label for="filter">🎚 Filter by minimum match %:</label>
        <input type="range" id="filterRange" min="0" max="100" value="0" oninput="filterScore(this.value)">
        <span id="filterValue">0%</span>

        <canvas id="skillsChart" style="max-width:600px;margin:20px 0;"></canvas>

        <div id="jobContainer">
        <?php 
            $stopwords = ['and','or','in','on','with','the','for','a','an','of','is','to','from','by','at','as','are','be','this','that','it','your','our','you'];
            $allMatchedSkills = [];
            foreach ($jobs as $job):
                $score = $rankings[$job['job_id']] ?? null;
                if ($score !== null && $score > 0):
                    $jobText = strtolower($job['title'] . ' ' . $job['description'] . ' ' . $job['requirements']);
                    $resumeWords = array_unique(preg_split('/\W+/', strtolower($resume)));
                    $resumeWords = array_diff($resumeWords, $stopwords);
                    $reasoning = array_intersect($resumeWords, preg_split('/\W+/', $jobText));
                    foreach ($reasoning as $word) $allMatchedSkills[] = $word;
                    $reasoningList = implode(', ', array_unique(array_slice($reasoning, 0, 5)));

                    $stars = '⭐';
                    if ($score >= 90) $stars = '⭐⭐⭐⭐⭐';
                    elseif ($score >= 70) $stars = '⭐⭐⭐⭐';
                    elseif ($score >= 50) $stars = '⭐⭐⭐';
                    elseif ($score >= 30) $stars = '⭐⭐';
        ?>
        <div class="job-card" data-score="<?= $score ?>">
            <h3><?= htmlspecialchars($job['title']) ?> <span class="stars">(<?= $stars ?>)</span></h3>
            <div class="meter"><span style="width: <?= $score ?>%;"></span></div>
            <p><strong>Description:</strong> <?= htmlspecialchars($job['description']) ?></p>
            <p><strong>Requirements:</strong> <?= htmlspecialchars($job['requirements']) ?></p>
            <p class="match-reason"><em>Matched on:</em> <?= htmlspecialchars($reasoningList ?: 'General skills & experience') ?></p>
            <form method="POST" action="apply.php">
                <input type="hidden" name="job_id" value="<?= $job['job_id'] ?>">
                <input type="submit" value="🚀 Apply Now">
            </form>
        </div>
        <?php endif; endforeach; ?>
        </div>

        <script>
            const skillData = <?php echo json_encode(array_count_values($allMatchedSkills)); ?>;
            const ctx = document.getElementById('skillsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(skillData),
                    datasets: [{
                        label: 'Matched Skill Frequency',
                        data: Object.values(skillData),
                        backgroundColor: '#0a66c2'
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true } },
                    plugins: {
                        legend: { display: false },
                        title: { display: true, text: '🧠 Top Keywords Matched From Your Resume' }
                    }
                }
            });

            function filterScore(minScore) {
                document.getElementById("filterValue").textContent = minScore + "%";
                document.querySelectorAll('.job-card').forEach(card => {
                    let score = parseInt(card.getAttribute('data-score'));
                    card.style.display = score >= minScore ? 'block' : 'none';
                });
            }
        </script>
    <?php else: ?>
        <div class="alert">❌ No matched jobs returned by AI.</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn" style="margin-top:20px;">⬅ Back to Dashboard</a>
</div>

<div class="footer">© 2025 MoroccoHire. AI-Powered Career Matching Platform.</div>

</body>
</html>
