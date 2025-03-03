<?php
include '../database/config.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $fullname = trim($_POST['fullname']);
    $user_type = trim($_POST['user_type']);

    // Validate inputs
    if (empty($fullname) || empty($user_type)) {
        echo "Error: All fields are required!";
        exit;
    }

    // Update query
    $sql = "UPDATE users SET fullname = ?, user_type = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $user_type, $user_id);

    if ($stmt->execute()) {
        // Redirect back to scoreboard with success message
        header("Location: admin_scoreboard.php?success=User updated successfully");
        exit;
    } else {
        echo "Error updating user: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
