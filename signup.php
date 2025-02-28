<?php
include 'database/config.php'; // Ensure correct path

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $userType = trim($_POST['user_type']);

    // Validate inputs
    if (empty($fullname) || empty($username) || empty($password) || empty($userType)) {
        $error = "All fields are required!";
    } else {
        // Sanitize and escape username for security
        $username = htmlspecialchars(mysqli_real_escape_string($conn, $username));
        $fullname = htmlspecialchars(mysqli_real_escape_string($conn, $fullname));

        // Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if username exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if (!$checkStmt) {
            die("Error preparing checkStmt: " . $conn->error);
        }
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "Username already exists. Please choose another.";
        } else {
            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (fullname, username, password, user_type) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                die("Error preparing stmt: " . $conn->error);
            }

            $stmt->bind_param("ssss", $fullname, $username, $hashedPassword, $userType);
            
            if ($stmt->execute()) {
                // If user is a student, insert into students table
                if (strtolower($userType) === "student") {
                    $studentStmt = $conn->prepare("INSERT INTO students (username, full_name, student_status, score) VALUES (?, ?, ?, ?)");
                    if (!$studentStmt) {
                        die("Error preparing studentStmt: " . $conn->error);
                    }

                    $student_status = "active"; // Default status
                    $score = 0; // Default score
                    $studentStmt->bind_param("sssi", $username, $fullname, $student_status, $score);
                    $studentStmt->execute();
                    $studentStmt->close();
                }

                // Redirect to login
                header("Location: login.php");
                exit();
            } else {
                die("Error executing stmt: " . $stmt->error);
            }

            $stmt->close();
        }
        $checkStmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: url('https://t4.ftcdn.net/jpg/03/30/99/59/360_F_330995960_bY9sCgdaQCq2AW7C8OODzxWeLmxuFDTg.jpg') no-repeat center center/cover;
        }
        .signup-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            max-width: 400px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-link {
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            body {
                padding: 20px;
            }
            .signup-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form action="signup.php" method="POST">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="user_type" required>
                <option value="Student">Student</option>
                <option value="Not Student">Not Student</option>
            </select>
            <button type="submit">Sign Up</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Sign In</a></p>
    </div>
</body>
</html>
