<?php
include '../database/config.php';

// Fetch users securely using prepared statement
$sql = "SELECT id, fullname, username, user_type, score FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Scoreboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Background Styling */
        body {
            background: url('https://t4.ftcdn.net/jpg/03/30/99/59/360_F_330995960_bY9sCgdaQCq2AW7C8OODzxWeLmxuFDTg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
        }
        .form-label, .modal-title, .modal-body {
            color:rgb(0, 0, 0);
        }
        /* Overlay */
        .overlay {
            background: rgba(0, 0, 0, 0.6);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* Navbar Styling */
        .navbar {
            background: rgba(0, 0, 0, 0.85) !important;
        }

        .navbar-brand, .nav-link {
            color: #fff !important;
        }

        .nav-link.active {
            font-weight: bold;
            border-bottom: 2px solid #ffc107;
        }

        /* Container Styling */
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            color: black;
        }

        /* Table Styling */
        .table-hover tbody tr:hover {
            background:rgb(68, 139, 211);
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 12px;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Search Bar */
        .search-box {
            width: 300px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_students.php">Manage Students</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_scoreboard.php">Scoreboard</a></li>
                <li class="nav-item"><a class="nav-link" href="./logout.php">logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container">
    <h2 class="text-center mb-4">Admin Scoreboard</h2>
    
    <!-- Search Bar -->
    <input type="text" class="form-control search-box" id="search" placeholder="Search by name or username...">

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>User Type</th>
                <th>Score</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="userTable">
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo ucfirst(htmlspecialchars($row['user_type'])); ?></td>
                <td><?php echo htmlspecialchars($row['score']); ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                        onclick="fillEditForm(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['fullname']); ?>', '<?php echo htmlspecialchars($row['user_type']); ?>')">
                        Edit
                    </button>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#resetModal" 
                        onclick="setResetId(<?php echo $row['id']; ?>)">
                        Reset
                    </button>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- RESET SCORE MODAL -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Reset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to reset this user's score to 0?
            </div>
            <div class="modal-footer">
                <form action="reset_score.php" method="POST">
                    <input type="hidden" name="user_id" id="resetUserId">
                    <button type="submit" class="btn btn-danger">Reset</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- EDIT USER MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="edit_user.php" method="POST">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="mb-3">
                        <label for="editFullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="fullname" id="editFullName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUserType" class="form-label">User Type</label>
                        <select class="form-control" name="user_type" id="editUserType">
                            <option value="student">Student</option>
                            <option value="not student">Not Student</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    // Fill the edit modal form with user data
function fillEditForm(id, fullName, userType) {
    document.getElementById('editUserId').value = id;
    document.getElementById('editFullName').value = fullName;
    document.getElementById('editUserType').value = userType;
}

// Set user ID for reset modal
function setResetId(id) {
    document.getElementById('resetUserId').value = id;
}


// Search Filter
document.getElementById("search").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#userTable tr");
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

</body>
</html>
