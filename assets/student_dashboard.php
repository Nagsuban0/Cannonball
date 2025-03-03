<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];

// Database connection
include('../database/config.php');

// Fetch logged-in student details
$query = "SELECT fullname, user_type, score FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Fetch all students
$query_all = "SELECT fullname, user_type, score FROM users";
$result_all = $conn->query($query_all);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('https://media.istockphoto.com/id/1250515454/vector/pirate-ship-in-a-bay-with-trunks-of-treasure.jpg?s=612x612&w=0&k=20&c=D5YXZyOJO3BsO_rHCNAU5itfoWTsRflUHPi43jZlrMY=') no-repeat center center fixed;
            background-size: cover;
            color: white;
            text-align: center;
        }
        .container {
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            display: inline-block;
            margin-top: 50px;
            border-radius: 10px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background: #ffcc00;
            color: black;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }
        .btn:hover {
            background: #ff9900;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .modal-content {
            background-color: #222;
            color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 60%;
            text-align: center;
        }
        .close {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid white;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #444;
        }
        .modal-buttons {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($student['fullname']); ?>!</h1>
        <p>Your Score: <?php echo $student['score']; ?></p>
        <div class="modal-buttons">
                <a href="./game_template.php" class="btn">Start Game</a>
                <a href="./logout.php" class="btn">Logout</a>
            </div>
        <button class="btn" onclick="openModal()">View Dashboard</button>
    </div>
    
    <!-- Dashboard Modal -->
    <div id="dashboardModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Student Dashboard</h2>
            <table>
                <tr>
                    <th>Full Name</th>
                    <th>User Type</th>
                    <th>Score</th>
                </tr>
                <?php while ($row = $result_all->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                    <td><?php echo $row['score']; ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
    
    <script>
        function openModal() {
            document.getElementById("dashboardModal").style.display = "block";
        }
        function closeModal() {
            document.getElementById("dashboardModal").style.display = "none";
        }
    </script>
</body>
</html>
