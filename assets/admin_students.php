<?php
// Include database configuration
include '../database/config.php';

// Handle student deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete user record
    $conn->query("DELETE FROM users WHERE id = $delete_id");

    // Also delete from students table if they exist
    $conn->query("DELETE FROM students WHERE username = (SELECT username FROM users WHERE id = $delete_id)");

    // Redirect to refresh the list
    header("Location: admin_students.php");
    exit();
}

// Fetch all users from the database
$student_result = $conn->query("
    SELECT id, fullname, username, user_type FROM users
");

if ($student_result === false) {
    die("Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://t4.ftcdn.net/jpg/03/30/99/59/360_F_330995960_bY9sCgdaQCq2AW7C8OODzxWeLmxuFDTg.jpg') no-repeat center center fixed;
            background-size: cover;
            text-align: center;
        }
        .header {
            background: #343a40;
            color: white;
            padding: 15px;
            font-size: 20px;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 10px;
        }
        th {
            background: #007bff;
            color: white;
        }
        .nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .nav a {
            text-decoration: none;
            color: #007bff;
            font-size: 18px;
        }
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .edit-btn {
            background: #ffc107;
            color: black;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
        }
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">Admin Panel - Student List</div>
    <div class="container">
        <h2>List of Users</h2>
        <div class="nav">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_scoreboard.php">Scoreboard</a>
            <a href="admin_students.php">List of Students</a>
            <a href="admin_logout.php">Logout</a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $student_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td>
                        <?php 
                        echo ($row['user_type'] === "Student") 
                            ? "<span style='color: green;'>Student</span>" 
                            : "<span style='color: red;'>Not Student</span>"; 
                        ?>
                    </td>
                    <td>
                        <a href="edit_student.php?id=<?php echo $row['id']; ?>">
                            <button class="edit-btn">Edit</button>
                        </a>
                        <a href="admin_students.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">
                            <button class="delete-btn">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
