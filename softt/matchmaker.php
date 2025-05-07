<?php

function getJobRecommendations($resumeText, $jobs) {
    // Expanded skill set
    $skillKeywords = [
        // Frontend
        "html", "css", "sass", "scss", "less", "bootstrap", "tailwind", "javascript", "typescript",
        "react", "angular", "vue", "next.js", "jquery", "backbone", "ember", "astro", "vite", "webpack",
        "babel", "npm", "yarn", "eslint", "storybook", "figma", "material-ui", "chakra-ui", "redux",
        "mobx", "zustand", "rxjs", "framer motion", "three.js", "canvas", "webgl",

        // Backend
        "php", "laravel", "symfony", "codeigniter", "cakephp", "node.js", "express", "fastify",
        "python", "django", "flask", "pyramid", "sqlalchemy", "ruby", "rails", "sinatra",
        "java", "spring", "spring boot", "kotlin", "groovy", "scala", "c#", ".net", "asp.net",
        "go", "gin", "echo", "fiber", "c", "c++", "rust", "perl", "elixir", "phoenix",

        // Database
        "mysql", "postgresql", "sqlite", "mongodb", "redis", "cassandra", "mariadb", "oracle",
        "mssql", "dynamodb", "neo4j", "elasticsearch", "firebase", "supabase", "influxdb",
        "couchdb", "bigquery", "timescaledb", "clickhouse",

        // DevOps
        "docker", "kubernetes", "jenkins", "gitlab ci", "github actions", "travis ci", "ansible",
        "terraform", "pulumi", "helm", "aws", "azure", "gcp", "digitalocean", "linode", "vagrant",
        "cloudflare", "nginx", "apache", "ssl", "linux", "bash", "powershell", "zsh",

        // Data
        "pandas", "numpy", "matplotlib", "seaborn", "scikit-learn", "tensorflow", "keras",
        "pytorch", "huggingface", "openai", "xgboost", "lightgbm", "statsmodels", "sql", "r", "julia",
        "dataframe", "hadoop", "spark", "databricks", "etl", "data wrangling", "data cleaning", "excel",
        "tableau", "power bi", "dash", "plotly", "superset",

        // Soft Skills
        "teamwork", "communication", "leadership", "problem solving", "critical thinking", "creativity",
        "collaboration", "adaptability", "time management", "project management", "scrum", "agile",
        "kanban", "product management", "decision making", "empathy", "responsibility", "initiative"
    ];

    $jobCategories = [
        "frontend" => ["html", "css", "react", "javascript", "ui", "ux", "tailwind", "typescript", "vue", "angular"],
        "backend" => ["php", "mysql", "node", "python", "java", "laravel", "django", "flask", "api", "express"],
        "data"     => ["python", "sql", "pandas", "machine learning", "analyst", "numpy", "keras", "pytorch"],
        "devops"   => ["docker", "kubernetes", "aws", "ci", "cd", "infrastructure", "ansible", "jenkins"]
    ];

    $resume = strtolower($resumeText);
    $resumeWords = array_unique(preg_split('/\W+/', $resume));

    // Skill Match
    $matchedSkills = array_intersect($resumeWords, $skillKeywords);
    $skillScoreBase = count($skillKeywords);
    $skillScore = $skillScoreBase > 0 ? (count($matchedSkills) / $skillScoreBase) * 100 : 0;

    // Experience Estimate
    $experienceScore = preg_match('/\b(\d{1,2})\+?\s*(years|yrs)/', $resume, $match)
        ? min(intval($match[1]) * 10, 100)
        : (stripos($resume, "senior") !== false ? 80 : 30);

    $scores = [];

    foreach ($jobs as $job) {
        $jobText = strtolower($job['title'] . ' ' . $job['description'] . ' ' . $job['requirements']);
        $jobWords = preg_split('/\W+/', $jobText);

        // Role/category match
        $roleScore = 0;
        foreach ($jobCategories as $category => $keywords) {
            $overlap = array_intersect($keywords, $jobWords);
            $resumeOverlap = array_intersect($keywords, $resumeWords);
            if (count($overlap) > 0 && count($resumeOverlap) > 0) {
                $roleScore = 80;
                break;
            }
        }

        // Skill match per job
        $jobSkillMatches = array_intersect($resumeWords, $jobWords);
        $jobSkillScore = count($jobSkillMatches) / count($resumeWords) * 100;

        // Final Score (Weighted)
        $finalScore = (0.5 * $jobSkillScore) + (0.3 * $roleScore) + (0.2 * $experienceScore);
        $scores[] = "{$job['job_id']} - " . round(min($finalScore, 100));
    }

    return implode("\n", $scores);
}
?>



