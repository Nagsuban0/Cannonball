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

// Fetch all users from the database, including password (in plain text)
$student_result = $conn->query("SELECT id, fullname, username, user_type, password FROM users");

if ($student_result === false) {
    die("Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://t4.ftcdn.net/jpg/03/30/99/59/360_F_330995960_bY9sCgdaQCq2AW7C8OODzxWeLmxuFDTg.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        tr:hover {
            background: #e9ecef;
        }

        .btn-action {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s ease-in-out;
        }
        .btn-edit {
            background: #ffc107;
            color: black;
        }
        .btn-edit:hover {
            background: #e0a800;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background: #c82333;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_students.php">Manage Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_scoreboard.php">Scoreboard</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container">
    <h2 class="text-center">Student List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Type</th>
                <th>Password</th> <!-- Added column for password -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $student_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td>
                        <?php 
                        echo ($row['user_type'] === "Student") 
                            ? "<span class='text-success fw-bold'>Student</span>" 
                            : "<span class='text-danger fw-bold'>Not Student</span>"; 
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td> <!-- Display password -->
                    <td>
                        <!-- Edit Button (Triggers Modal) -->
                        <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?php echo $row['id']; ?>" data-fullname="<?php echo $row['fullname']; ?>" data-username="<?php echo $row['username']; ?>" data-usertype="<?php echo $row['user_type']; ?>" data-password="<?php echo $row['password']; ?>">Edit</button>
                        <!-- Delete Button -->
                        <a href="admin_students.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">
                            <button class="btn-action btn-delete">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="edit_student.php" method="POST">
            <input type="hidden" name="id" id="userId">
            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="fullname" id="fullname" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" required>
            </div>
            <div class="mb-3">
                <label for="usertype" class="form-label">User Type</label>
                <select class="form-select" name="user_type" id="usertype" required>
                    <option value="Student">Student</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password">
                <button type="button" class="btn btn-link" id="viewPasswordBtn" onclick="togglePassword()">View Password</button>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    // Pass data from the button to the modal
    var editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var userId = button.getAttribute('data-id');
            var fullname = button.getAttribute('data-fullname');
            var username = button.getAttribute('data-username');
            var userType = button.getAttribute('data-usertype');
            var password = button.getAttribute('data-password');
            
            document.getElementById('userId').value = userId;
            document.getElementById('fullname').value = fullname;
            document.getElementById('username').value = username;
            document.getElementById('usertype').value = userType;
            document.getElementById('password').value = password;
        });
    });

    // Toggle password visibility
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var passwordFieldType = passwordField.type;
        if (passwordFieldType === "password") {
            passwordField.type = "text";
            document.getElementById("viewPasswordBtn").innerText = "Hide Password";
        } else {
            passwordField.type = "password";
            document.getElementById("viewPasswordBtn").innerText = "View Password";
        }
    }
</script>

</body>
</html>
