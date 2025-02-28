<?php
// Include database connection
include('../database/config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];
$fullname = "Unknown User"; // Default value if query fails
$score = 0; // Default score

// Fetch user details and score
$userQuery = "SELECT fullname, score FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $userQuery);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $fullname, $score);
    if (!mysqli_stmt_fetch($stmt)) {
        // Handle error fetching data (e.g., no result found)
        error_log("No user found with ID: " . $user_id);
    }
    mysqli_stmt_close($stmt);
} else {
    // Handle error preparing the query
    error_log("Query Error: " . mysqli_error($conn));
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Scoreboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
        }

        .card {
            margin-top: 20px;
        }

        .score-board {
            margin-top: 50px;
        }

        .profile-info {
            text-align: center;
        }

        .score-info {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Game Dashboard</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">

        <!-- Profile Card -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>User Profile</h4>
            </div>
            <div class="card-body">
                <div class="profile-info">
                    <h5 class="card-title"><?php echo htmlspecialchars($fullname); ?></h5>
                    <p class="card-text">Welcome to your dashboard</p>
                    <p class="score-info">Your Score: <?php echo $score; ?> Points</p>
                </div>
            </div>
        </div>

        <!-- Scoreboard Section -->
        <div class="score-board">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4>Scoreboard</h4>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Your Current Score:</h5>
                    <p class="card-text">Congratulations, <?php echo htmlspecialchars($fullname); ?>! Your score is now <strong><?php echo $score; ?></strong> points.</p>
                    <p class="card-text">Keep up the great work and continue playing to improve your score!</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
